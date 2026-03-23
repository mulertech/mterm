<?php

namespace MulerTech\MTerm\Command;

/**
 * Interface CommandInterface.
 *
 * @author Sébastien Muler
 */
interface CommandInterface
{
    /**
     * Get the command name.
     */
    public function getName(): string;

    /**
     * Get the command description.
     */
    public function getDescription(): string;

    /**
     * Display help information for this command.
     */
    public function showHelp(): void;

    /**
     * Execute the command.
     *
     * @param array<int, mixed> $args Command arguments
     *
     * @return int Exit code (0 for success, other values for errors)
     */
    public function execute(array $args = []): int;
}
