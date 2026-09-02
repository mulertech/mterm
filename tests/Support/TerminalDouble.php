<?php

namespace MulerTech\MTerm\Tests\Support;

use MulerTech\MTerm\Core\Input\InputReader;
use MulerTech\MTerm\Core\Output\BufferedOutput;
use MulerTech\MTerm\Core\Terminal;

/**
 * A terminal reading a fixed sequence of bytes and displaying into memory.
 */
class TerminalDouble
{
    public readonly Terminal $terminal;
    public readonly BufferedOutput $output;
    public readonly RecordingTerminalMode $mode;

    public function __construct(string $input = '', bool $decorated = false)
    {
        $this->output = new BufferedOutput($decorated);
        $this->mode = new RecordingTerminalMode();
        $this->terminal = new Terminal($this->output, new InputReader(self::stream($input)), $this->mode);
    }

    /**
     * Everything displayed so far.
     */
    public function display(): string
    {
        return $this->output->content();
    }

    /**
     * A real file descriptor rather than a memory stream: only the former can
     * be waited upon, which is how an escape sequence is told from a lone
     * Escape key.
     *
     * @return resource
     */
    public static function stream(string $content)
    {
        $stream = tmpfile();

        if (false === $stream) {
            throw new \RuntimeException('Unable to open a temporary stream.');
        }

        fwrite($stream, $content);
        rewind($stream);

        return $stream;
    }
}
