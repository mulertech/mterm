<?php

namespace MulerTech\MTerm\Core;

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

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function getTerminal(): Terminal
    {
        return $this->terminal;
    }

    public function getCommandRunner(): CommandRunner
    {
        return $this->commandRunner;
    }

    public function run(): void
    {
        // Point d'entrée principal pour l'exécution de l'application
        $this->terminal->writeLine("MTerm Application Started", "green");

        // Logique d'application à implémenter
    }
}