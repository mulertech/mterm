<?php

namespace MulerTech\MTerm\Command;

use MulerTech\MTerm\Core\Color;
use MulerTech\MTerm\Core\Terminal;

/**
 * Help command to display available commands and usage information.
 *
 * @author Sébastien Muler
 */
class HelpCommand extends AbstractCommand
{
    private CommandRegistry $registry;

    public function __construct(Terminal $terminal, CommandRegistry $registry)
    {
        parent::__construct($terminal);
        $this->registry = $registry;
        $this->name = 'help';
        $this->description = 'Display help information about available commands';
    }

    /**
     * @param array<int, mixed> $args Command arguments
     *
     * @return int Exit code (0 for success, other values for errors)
     */
    public function execute(array $args = []): int
    {
        if (!empty($args) && isset($args[0]) && is_string($args[0])) {
            return $this->showCommandHelp($args[0]);
        }

        return $this->showAllCommands();
    }

    /**
     * Show help for a specific command.
     */
    private function showCommandHelp(string $commandName): int
    {
        $command = $this->registry->get($commandName);

        if (null === $command) {
            $this->terminal->writeLine("Command '{$commandName}' not found", Color::Red);

            return 1;
        }

        $this->terminal->writeLine('COMMAND', Color::Green, true);
        $this->terminal->writeLine("  {$command->getName()}", Color::White);
        $this->terminal->writeLine('');
        $this->terminal->writeLine('DESCRIPTION', Color::Green, true);
        $this->terminal->writeLine("  {$command->getDescription()}", Color::White);
        $this->terminal->writeLine('');
        $command->showHelp();

        return 0;
    }

    /**
     * Show all available commands.
     */
    private function showAllCommands(): int
    {
        $this->terminal->writeLine('Available Commands:', Color::Green, true);
        $this->terminal->writeLine('');

        $commands = $this->registry->getAll();
        ksort($commands);

        foreach ($commands as $name => $command) {
            $this->terminal->write("  {$name}", Color::Yellow);
            $this->terminal->writeLine(" - {$command->getDescription()}");
        }

        $this->terminal->writeLine('');
        $this->terminal->writeLine('For more information about a command, type:');
        $this->terminal->writeLine('  help <command>', Color::Cyan);

        return 0;
    }
}
