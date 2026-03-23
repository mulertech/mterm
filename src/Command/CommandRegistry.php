<?php

namespace MulerTech\MTerm\Command;

use MulerTech\MTerm\Core\Terminal;

/**
 * Class CommandRegistry.
 *
 * @author Sébastien Muler
 */
class CommandRegistry
{
    /**
     * @var array<string,CommandInterface> Registered commands
     */
    private array $commands = [];

    /**
     * Register a command.
     *
     * @param CommandInterface $command Command to register
     */
    public function register(CommandInterface $command): self
    {
        if (!$this->has('help')) {
            // Ensure help command is always registered
            $this->commands['help'] = new HelpCommand(new Terminal(), $this);
        }

        $this->commands[$command->getName()] = $command;

        return $this;
    }

    /**
     * Check if a command exists by name.
     *
     * @param string $name Command name
     */
    public function has(string $name): bool
    {
        return isset($this->commands[$name]);
    }

    /**
     * Get a command by name.
     *
     * @param string $name Command name
     *
     * @return CommandInterface|null Command or null if not found
     */
    public function get(string $name): ?CommandInterface
    {
        return $this->commands[$name] ?? null;
    }

    /**
     * Get all registered commands.
     *
     * @return array<string,CommandInterface>
     */
    public function getAll(): array
    {
        return $this->commands;
    }

    /**
     * Execute a command by name with arguments.
     *
     * @param string            $name Command name
     * @param array<int, mixed> $args Command arguments
     *
     * @return int Exit code
     *
     * @throws \InvalidArgumentException When command doesn't exist
     */
    public function execute(string $name, array $args = []): int
    {
        $command = $this->get($name);

        if (null === $command) {
            throw new \InvalidArgumentException("Command '$name' not found");
        }

        return $command->execute($args);
    }
}
