<?php

namespace MulerTech\MTerm\Core;

use MulerTech\MTerm\Core\Input\InputReader;
use MulerTech\MTerm\Core\Input\KeyPress;
use MulerTech\MTerm\Core\Output\OutputInterface;
use MulerTech\MTerm\Core\Output\StreamOutput;

/**
 * Class Terminal.
 *
 * Every escape sequence goes through a single gate: an output that is not a
 * terminal receives the text alone, so a redirected display stays readable.
 *
 * @author Sébastien Muler
 */
class Terminal
{
    public function __construct(
        private readonly OutputInterface $output = new StreamOutput(),
        private readonly InputReader $input = new InputReader(),
        private readonly TerminalMode $mode = new TerminalMode(),
    ) {
    }

    public function getOutput(): OutputInterface
    {
        return $this->output;
    }

    public function getInput(): InputReader
    {
        return $this->input;
    }

    public function read(?string $prompt = null): string
    {
        if (null !== $prompt) {
            $this->write($prompt);
        }

        return $this->input->readLine();
    }

    /**
     * One whole character, accents and emoji included.
     */
    public function readChar(?string $prompt = null): string
    {
        if (null !== $prompt) {
            $this->write($prompt);
        }

        return $this->input->readCharacter();
    }

    /**
     * One press, with arrows and function keys resolved to the key they name.
     */
    public function readKey(?string $prompt = null): KeyPress
    {
        if (null !== $prompt) {
            $this->write($prompt);
        }

        return $this->input->readKey();
    }

    /**
     * Drop the keys typed ahead, so the next read answers the question asked next.
     */
    public function discardPendingInput(): void
    {
        $this->input->discardPending();
    }

    public function write(string $text, ?Color $color = null, bool $bold = false): void
    {
        if (null === $color || !$this->supportsAnsi()) {
            $this->output->write($text);

            return;
        }

        $this->output->write($color->sequence($bold).$text.Color::RESET);
    }

    public function writeLine(string $text = '', ?Color $color = null, bool $bold = false): void
    {
        $this->write($text.PHP_EOL, $color, $bold);
    }

    /**
     * Erase the screen and its scrollback, and put the cursor back at its top
     * left corner.
     *
     * The scrollback goes too: some terminals — PhpStorm's among them — answer
     * the erasing of the screen by pushing it into the scrollback, and the last
     * line of what came before stays visible above what is drawn next.
     */
    public function clear(): void
    {
        $this->writeSequence("\033[H\033[2J\033[3J");
    }

    /**
     * Erase the line the cursor sits on, and return to its first column.
     */
    public function clearLine(): void
    {
        $this->writeSequence("\r\033[2K");
    }

    /**
     * Place the cursor, counting rows and columns from one.
     */
    public function moveCursor(int $row, int $column): void
    {
        if ($row < 1 || $column < 1) {
            throw new \InvalidArgumentException(sprintf('Cursor coordinates start at 1, got row %d and column %d.', $row, $column));
        }

        $this->writeSequence("\033[{$row};{$column}H");
    }

    public function hideCursor(): void
    {
        $this->writeSequence("\033[?25l");
    }

    public function showCursor(): void
    {
        $this->writeSequence("\033[?25h");
    }

    /**
     * Read keys one by one, without echo. Restored whatever happens next.
     */
    public function enableRawMode(): void
    {
        $this->mode->enableRaw();
    }

    public function disableRawMode(): void
    {
        $this->mode->restore();
    }

    public function isRawMode(): bool
    {
        return $this->mode->isRaw();
    }

    public function supportsAnsi(): bool
    {
        return $this->output->isDecorated();
    }

    private function writeSequence(string $sequence): void
    {
        if ($this->supportsAnsi()) {
            $this->output->write($sequence);
        }
    }
}
