<?php

namespace MulerTech\MTerm\Command;

use MulerTech\MTerm\Core\Terminal;

/**
 * Abstract command class
 * @package MulerTech\MTerm
 * @author Sébastien Muler
 */
abstract class AbstractCommand implements CommandInterface
{
    protected Terminal $terminal;
    protected string $name;
    protected string $description;

    /**
     * @param Terminal $terminal
     */
    public function __construct(Terminal $terminal)
    {
        $this->terminal = $terminal;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * Display help information for this command
     * @return void
     */
    public function showHelp(): void
    {
        $this->terminal->writeLine($this->getName() . ": " . $this->getDescription(), "cyan");
    }
}
