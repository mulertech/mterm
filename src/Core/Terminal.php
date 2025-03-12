<?php

namespace MulerTech\MTerm\Core;

class Terminal
{
    private const COLORS = [
        'black' => '0;30',
        'red' => '0;31',
        'green' => '0;32',
        'yellow' => '0;33',
        'blue' => '0;34',
        'magenta' => '0;35',
        'cyan' => '0;36',
        'white' => '0;37',
    ];

    public function read(string $prompt = null): string
    {
        if ($prompt !== null) {
            $this->write($prompt);
        }

        return trim(fgets(STDIN));
    }

    public function write(string $text, string $color = null): void
    {
        if ($color !== null && isset(self::COLORS[$color]) && $this->supportsAnsi()) {
            echo "\033[" . self::COLORS[$color] . "m" . $text . "\033[0m";
        } else {
            echo $text;
        }
    }

    public function writeLine(string $text, string $color = null): void
    {
        $this->write($text . PHP_EOL, $color);
    }

    public function clear(): void
    {
        if ($this->supportsAnsi()) {
            echo "\033[2J\033[H";
        } else {
            // Fallback for Windows or non-ANSI terminals
            for ($i = 0; $i < 50; $i++) {
                echo PHP_EOL;
            }
        }
    }

    private function supportsAnsi(): bool
    {
        return DIRECTORY_SEPARATOR === '/' ||
            (function_exists('sapi_windows_vt100_support') &&
            @sapi_windows_vt100_support(STDOUT));
    }
}