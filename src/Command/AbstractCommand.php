<?php

namespace MulerTech\MTerm\Command;

use MulerTech\MTerm\Core\Terminal;

abstract class AbstractCommand implements CommandInterface
{
    protected Terminal $terminal;
    protected string $name;
    protected string $description;

    public function __construct(Terminal $terminal)
    {
        $this->terminal = $terminal;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * Display help information for this command
     */
    public function showHelp(): void
    {
        $this->terminal->writeLine($this->getName() . ": " . $this->getDescription(), "cyan");
    }

    /**
     * Validate command arguments
     *
     * @param array $args Arguments to validate
     * @return bool True if arguments are valid
     */
    protected function validateArgs(array $args): bool
    {
        return true;
    }
}