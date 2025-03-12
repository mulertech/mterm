<?php

namespace MulerTech\MTerm\Utils;

class Output
{
    /**
     * Output text to the terminal
     *
     * @param string $text Text to output
     * @return void
     */
    public function write(string $text): void
    {
        echo $text;
    }

    /**
     * Output text with a newline to the terminal
     *
     * @param string $text Text to output
     * @return void
     */
    public function writeLine(string $text): void
    {
        echo $text . PHP_EOL;
    }

    /**
     * Clear the terminal screen
     *
     * @return void
     */
    public function clear(): void
    {
        if ($this->isWindows()) {
            system('cls');
        } else {
            system('clear');
        }
    }

    /**
     * Check if the current OS is Windows
     *
     * @return bool
     */
    protected function isWindows(): bool
    {
        return DIRECTORY_SEPARATOR === '\\';
    }
}