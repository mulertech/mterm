<?php

namespace MulerTech\MTerm\Core;

/**
 * Class Application
 * @package MulerTech\MTerm
 * @author Sébastien Muler
 */
class Application
{
    private Terminal $terminal;
    private CommandRunner $commandRunner;
    private static ?Application $instance = null;

    private function __construct()
    {
        $this->terminal = new Terminal();
        $this->commandRunner = new CommandRunner();
    }

    /**
     * @return self
     */
    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * @return Terminal
     */
    public function getTerminal(): Terminal
    {
        return $this->terminal;
    }

    /**
     * @return CommandRunner
     */
    public function getCommandRunner(): CommandRunner
    {
        return $this->commandRunner;
    }

    /**
     * @return void
     */
    public function run(): void
    {
        // Point d'entrée principal pour l'exécution de l'application
        $this->terminal->writeLine("MTerm Application Started", "green");

        // Logique d'application à implémenter
    }
}