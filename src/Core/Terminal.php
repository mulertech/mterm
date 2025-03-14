<?php

namespace MulerTech\MTerm\Core;

/**
 * Class Terminal
 * @package MulerTech\MTerm
 * @author Sébastien Muler
 */
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

    /**
     * @param string|null $prompt
     * @return string
     */
    public function read(string $prompt = null): string
    {
        if ($prompt !== null) {
            $this->write($prompt);
        }

        return trim(fgets(STDIN));
    }

    /**
     * @param string|null $prompt
     * @return string
     */
    public function readChar(string $prompt = null): string
    {
        if ($prompt !== null) {
            $this->write($prompt);
        }

        return fgetc(STDIN);
    }

    /**
     * @param string $text
     * @param string|null $color
     * @return void
     */
    public function write(string $text, string $color = null): void
    {
        if ($color !== null && isset(self::COLORS[$color]) && $this->supportsAnsi()) {
            echo "\033[" . self::COLORS[$color] . "m" . $text . "\033[0m";
        } else {
            echo $text;
        }
    }

    /**
     * @param string $text
     * @param string|null $color
     * @return void
     */
    public function writeLine(string $text, string $color = null): void
    {
        $this->write($text . PHP_EOL, $color);
    }

    /**
     * @return void
     */
    public function clear(): void
    {
        if (DIRECTORY_SEPARATOR === '/') {
            system('clear');
        } else {
            system('cls');
        }
    }

    /**
     * @return void
     */
    public function specialMode(): void
    {
        system('stty -icanon -echo');
    }

    /**
     * @return void
     */
    public function normalMode(): void
    {
        system('stty icanon echo');
    }

    /**
     * @return bool
     */
    private function supportsAnsi(): bool
    {
        return DIRECTORY_SEPARATOR === '/' ||
            (function_exists('sapi_windows_vt100_support') &&
            @sapi_windows_vt100_support(STDOUT));
    }
}