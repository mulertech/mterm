<?php

namespace MulerTech\MTerm\Tests\Support;

use MulerTech\MTerm\Core\TerminalMode;

/**
 * A terminal mode that records the stty calls it would have made, so a test
 * never changes the mode of the terminal running it.
 */
class RecordingTerminalMode extends TerminalMode
{
    /**
     * @var list<string>
     */
    public array $calls = [];

    public function __construct(
        private readonly bool $terminalPresent = true,
        private readonly ?string $savedState = 'saved-state',
    ) {
    }

    protected function hasTerminal(): bool
    {
        return $this->terminalPresent;
    }

    protected function runStty(string $arguments): ?string
    {
        $this->calls[] = $arguments;

        return '-g' === $arguments ? $this->savedState : '';
    }
}
