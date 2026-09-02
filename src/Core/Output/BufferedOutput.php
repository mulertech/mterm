<?php

namespace MulerTech\MTerm\Core\Output;

/**
 * Output keeping everything in memory, so a display can be asserted upon.
 *
 * @author Sébastien Muler
 */
class BufferedOutput implements OutputInterface
{
    private string $buffer = '';

    public function __construct(private readonly bool $decorated = false)
    {
    }

    public function write(string $text): void
    {
        $this->buffer .= $text;
    }

    public function isDecorated(): bool
    {
        return $this->decorated;
    }

    /**
     * Everything written so far.
     */
    public function content(): string
    {
        return $this->buffer;
    }

    /**
     * Everything written so far, emptying the buffer.
     */
    public function fetch(): string
    {
        $content = $this->buffer;
        $this->buffer = '';

        return $content;
    }
}
