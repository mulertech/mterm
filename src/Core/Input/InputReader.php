<?php

namespace MulerTech\MTerm\Core\Input;

/**
 * Reads a stream by press rather than by byte: an escape sequence and a
 * multi-byte character each come back whole.
 *
 * @author Sébastien Muler
 */
class InputReader
{
    /**
     * How long to wait for the rest of an escape sequence before concluding the
     * Escape key was pressed alone. A terminal emits the whole sequence at once,
     * so any wait covers it; too short a one turns arrow keys into stray letters.
     */
    private const int ESCAPE_SEQUENCE_TIMEOUT_MICROSECONDS = 20000;

    /**
     * @var resource
     */
    private $stream;

    private ?bool $waitable = null;

    /**
     * @param resource|null $stream Defaults to the standard input
     */
    public function __construct($stream = null)
    {
        $stream ??= defined('STDIN') ? STDIN : fopen('php://stdin', 'rb');

        if (!is_resource($stream)) {
            throw new \RuntimeException('Unable to open the standard input stream.');
        }

        $this->stream = $stream;
    }

    /**
     * @return resource
     */
    public function stream()
    {
        return $this->stream;
    }

    /**
     * One line, without its terminator, or an empty string at end of input.
     */
    public function readLine(): string
    {
        $line = fgets($this->stream);

        return false === $line ? '' : trim($line);
    }

    /**
     * One whole character, however many bytes it takes.
     */
    public function readCharacter(): string
    {
        $byte = $this->readByte();

        return null === $byte ? '' : $this->completeCharacter($byte);
    }

    /**
     * One press, with escape sequences resolved to the key they name.
     */
    public function readKey(): KeyPress
    {
        $byte = $this->readByte();

        if (null === $byte) {
            return KeyPress::endOfInput();
        }

        return match ($byte) {
            "\033" => $this->readEscapeSequence(),
            "\n", "\r" => KeyPress::ofKey(Key::Enter),
            "\x7F", "\x08" => KeyPress::ofKey(Key::Backspace),
            "\t" => KeyPress::ofKey(Key::Tab),
            ' ' => KeyPress::ofKey(Key::Space, ' '),
            default => KeyPress::ofCharacter($this->completeCharacter($byte)),
        };
    }

    /**
     * Drop what was typed before the question it would answer.
     *
     * A key pressed while an action ran — Enter struck after a confirmation, a
     * key pressed to see whether the program is still alive — waits in the
     * terminal and answers the next prompt at once, before anyone has read what
     * the action displayed. Only a terminal is drained: a file or a pipe carries
     * input written in advance on purpose, every byte of it meant to be read.
     */
    public function discardPending(): void
    {
        // A console nothing can wait upon — Windows' — cannot tell a key typed
        // ahead from one yet to come: reading would block on the next press.
        if (!$this->isInteractive() || !($this->waitable ??= $this->isWaitable())) {
            return;
        }

        do {
            $read = [$this->stream];
            $write = null;
            $except = null;

            if (0 >= (int) stream_select($read, $write, $except, 0, 0)) {
                return;
            }

            $chunk = fread($this->stream, 1024);
        } while (false !== $chunk && '' !== $chunk);
    }

    /**
     * Whether a person types into this stream, rather than a file or a pipe feeding it.
     */
    protected function isInteractive(): bool
    {
        return stream_isatty($this->stream);
    }

    private function readByte(): ?string
    {
        $byte = fgetc($this->stream);

        return false === $byte ? null : $byte;
    }

    /**
     * Append the continuation bytes the leading one announces.
     */
    private function completeCharacter(string $leadingByte): string
    {
        $leading = ord($leadingByte);
        $length = match (true) {
            0xF0 === ($leading & 0xF8) => 4,
            0xE0 === ($leading & 0xF0) => 3,
            0xC0 === ($leading & 0xE0) => 2,
            default => 1,
        };

        $character = $leadingByte;

        for ($read = 1; $read < $length; ++$read) {
            $byte = $this->readByte();

            if (null === $byte) {
                break;
            }

            $character .= $byte;
        }

        return $character;
    }

    private function readEscapeSequence(): KeyPress
    {
        if (!$this->hasPendingInput()) {
            return KeyPress::ofKey(Key::Escape);
        }

        return match ($this->readByte()) {
            '[' => $this->readControlSequence(),
            'O' => $this->readApplicationKey(),
            default => KeyPress::ofKey(Key::Escape),
        };
    }

    /**
     * Read a CSI sequence, whose parameters run up to a final byte in 0x40-0x7E.
     */
    private function readControlSequence(): KeyPress
    {
        $sequence = '';

        while (true) {
            $byte = $this->readByte();

            if (null === $byte) {
                break;
            }

            $sequence .= $byte;
            $code = ord($byte);

            if ($code >= 0x40 && $code <= 0x7E) {
                break;
            }
        }

        $key = match ($sequence) {
            'A' => Key::Up,
            'B' => Key::Down,
            'C' => Key::Right,
            'D' => Key::Left,
            'H', '1~', '7~' => Key::Home,
            'F', '4~', '8~' => Key::End,
            '2~' => Key::Insert,
            '3~' => Key::Delete,
            '5~' => Key::PageUp,
            '6~' => Key::PageDown,
            '15~' => Key::F5,
            '17~' => Key::F6,
            '18~' => Key::F7,
            '19~' => Key::F8,
            '20~' => Key::F9,
            '21~' => Key::F10,
            '23~' => Key::F11,
            '24~' => Key::F12,
            default => Key::Escape,
        };

        return KeyPress::ofKey($key);
    }

    /**
     * Read an SS3 sequence, which terminals use for the first four function keys.
     */
    private function readApplicationKey(): KeyPress
    {
        $key = match ($this->readByte()) {
            'P' => Key::F1,
            'Q' => Key::F2,
            'R' => Key::F3,
            'S' => Key::F4,
            'H' => Key::Home,
            'F' => Key::End,
            default => Key::Escape,
        };

        return KeyPress::ofKey($key);
    }

    private function hasPendingInput(): bool
    {
        if (!($this->waitable ??= $this->isWaitable())) {
            // A stream nothing can wait upon — a file, a Windows console — has
            // either delivered the rest of the sequence already or has nothing
            // more to give: reading on is the only way to tell.
            return true;
        }

        $read = [$this->stream];
        $write = null;
        $except = null;

        return 0 < (int) stream_select($read, $write, $except, 0, self::ESCAPE_SEQUENCE_TIMEOUT_MICROSECONDS);
    }

    /**
     * Whether this stream accepts being waited upon at all.
     */
    private function isWaitable(): bool
    {
        $read = [$this->stream];
        $write = null;
        $except = null;

        set_error_handler(static fn (): bool => true);

        try {
            return false !== stream_select($read, $write, $except, 0, 0);
        } catch (\ValueError) {
            // The stream was dropped as unrepresentable, leaving nothing to wait on
            return false;
        } finally {
            restore_error_handler();
        }
    }
}
