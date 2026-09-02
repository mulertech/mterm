<?php

namespace MulerTech\MTerm\Core\Output;

/**
 * Destination of everything the library displays.
 *
 * @author Sébastien Muler
 */
interface OutputInterface
{
    /**
     * Write raw text, without any decoration of its own.
     */
    public function write(string $text): void;

    /**
     * Whether escape sequences written here are interpreted rather than stored.
     *
     * A file, a pipe or a terminal the user asked to keep plain answers false:
     * writing sequences to it would pollute the captured text.
     */
    public function isDecorated(): bool;
}
