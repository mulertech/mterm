<?php

namespace MulerTech\MTerm\Core;

/**
 * Raw mode, and the guarantee that the terminal comes back from it.
 *
 * The state is saved before being changed and restored by a shutdown handler as
 * well as by the signal handlers, so an exception or an interruption never
 * leaves the user with a terminal that has stopped echoing what they type.
 *
 * @author Sébastien Muler
 */
class TerminalMode
{
    private ?string $savedState = null;
    private bool $restorationRegistered = false;

    public function isRaw(): bool
    {
        return null !== $this->savedState;
    }

    /**
     * Read keys one by one, without echo.
     *
     * Without a terminal on the standard input — a pipe, a file, a test — there
     * is no mode to change and the call does nothing.
     */
    public function enableRaw(): void
    {
        if ($this->isRaw() || !$this->hasTerminal()) {
            return;
        }

        $state = $this->runStty('-g');

        if (null === $state || '' === trim($state)) {
            throw new \RuntimeException('Unable to read the terminal state through stty: raw mode would not be reversible.');
        }

        $this->savedState = trim($state);
        $this->registerRestoration();
        $this->runStty('-icanon -echo min 1 time 0');
    }

    /**
     * Put the terminal back exactly as it was found.
     */
    public function restore(): void
    {
        if (null === $this->savedState) {
            return;
        }

        $state = $this->savedState;
        $this->savedState = null;
        $this->runStty($state);
    }

    /**
     * Whether the standard input is a terminal whose mode can be changed.
     */
    protected function hasTerminal(): bool
    {
        return DIRECTORY_SEPARATOR === '/'
            && defined('STDIN')
            && stream_isatty(STDIN);
    }

    /**
     * @return string|null The output of stty, or null when it could not be run
     */
    protected function runStty(string $arguments): ?string
    {
        $output = shell_exec('stty '.$arguments.' 2>/dev/null');

        return is_string($output) ? $output : null;
    }

    private function registerRestoration(): void
    {
        if ($this->restorationRegistered) {
            return;
        }

        $this->restorationRegistered = true;

        register_shutdown_function($this->restore(...));

        if (!function_exists('pcntl_signal') || !function_exists('pcntl_async_signals')) {
            // Without pcntl, an interruption kills the process before any shutdown handler runs:
            // the terminal is left without echo, which is exactly what safe raw mode exists to
            // prevent. A real degradation cannot stay silent, and the message carries the remedy
            // because a user facing a blind terminal is in no position to look it up.
            fwrite(
                STDERR,
                'mterm: ext-pcntl is missing — an interruption will leave the terminal without echo.'
                .' Restore it with "stty sane".'.PHP_EOL
            );

            return;
        }

        pcntl_async_signals(true);

        foreach ($this->interruptionSignals() as $signal) {
            // Without restarting the interrupted system call: a wait the signal
            // breaks must hand control back to the engine, which runs this
            // handler, rather than resume and hold the signal back.
            pcntl_signal($signal, function (int $signal): void {
                $this->restore();
                $this->leaveTheLine();

                exit(128 + $signal);
            }, false);
        }
    }

    /**
     * Give the shell back a visible cursor on a line of its own: the program
     * leaves in the middle of a prompt, and a menu hides the cursor while it runs.
     */
    private function leaveTheLine(): void
    {
        if (defined('STDOUT') && stream_isatty(STDOUT)) {
            fwrite(STDOUT, "\033[?25h".PHP_EOL);
        }
    }

    /**
     * @return array<int, int>
     */
    private function interruptionSignals(): array
    {
        return array_values(array_filter([
            defined('SIGINT') ? SIGINT : null,
            defined('SIGTERM') ? SIGTERM : null,
            defined('SIGHUP') ? SIGHUP : null,
            defined('SIGQUIT') ? SIGQUIT : null,
        ], is_int(...)));
    }
}
