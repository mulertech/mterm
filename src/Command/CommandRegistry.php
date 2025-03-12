<?php

namespace MulerTech\MTerm\Command;

class CommandRegistry
{
    /**
     * @var CommandInterface[]
     */
    private array $commands = [];

    /**
     * Register a command
     *
     * @param CommandInterface $command Command to register
     * @return self
     */
    public function register(CommandInterface $command): self
    {
        $this->commands[$command->getName()] = $command;
        return $this;
    }

    /**
     * Check if a command exists by name
     *
     * @param string $name Command name
     * @return bool
     */
    public function has(string $name): bool
    {
        return isset($this->commands[$name]);
    }

    /**
     * Get a command by name
     *
     * @param string $name Command name
     * @return CommandInterface|null Command or null if not found
     */
    public function get(string $name): ?CommandInterface
    {
        return $this->commands[$name] ?? null;
    }

    /**
     * Get all registered commands
     *
     * @return array<string,CommandInterface>
     */
    public function getAll(): array
    {
        return $this->commands;
    }

    /**
     * Execute a command by name with arguments
     *
     * @param string $name Command name
     * @param array $args Command arguments
     * @return int Exit code
     * @throws \InvalidArgumentException When command doesn't exist
     */
    public function execute(string $name, array $args = []): int
    {
        if (!$this->has($name)) {
            throw new \InvalidArgumentException("Command '{$name}' not found");
        }

        return $this->get($name)->execute($args);
    }
}