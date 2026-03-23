<?php

namespace MulerTech\MTerm\Core;

/**
 * Class Terminal.
 *
 * @author Sébastien Muler
 */
class Terminal
{
    public const COLORS = [
        'black' => '0;30',
        'red' => '0;31',
        'green' => '0;32',
        'yellow' => '0;33',
        'blue' => '0;34',
        'magenta' => '0;35',
        'cyan' => '0;36',
        'white' => '0;37',
        'bold_black' => '1;30',
        'bold_red' => '1;31',
        'bold_green' => '1;32',
        'bold_yellow' => '1;33',
        'bold_blue' => '1;34',
        'bold_magenta' => '1;35',
        'bold_cyan' => '1;36',
        'bold_white' => '1;37',
    ];

    public function read(?string $prompt = null): string
    {
        if (null !== $prompt) {
            $this->write($prompt);
        }

        $resource = $this->inputStream();

        $line = $resource ? fgets($resource) : false;

        return $line ? trim($line) : '';
    }

    public function readChar(?string $prompt = null): string
    {
        if (null !== $prompt) {
            $this->write($prompt);
        }

        $resource = $this->inputStream();

        $char = $resource ? fgetc($resource) : '';

        return false === $char ? '' : $char;
    }

    /**
     * @param bool $bold Whether to make text bold
     */
    public function write(string $text, ?string $color = null, bool $bold = false): void
    {
        if (null !== $color && $this->supportsAnsi()) {
            $colorKey = $bold ? "bold_{$color}" : $color;
            if (isset(self::COLORS[$colorKey])) {
                echo "\033[".self::COLORS[$colorKey].'m'.$text."\033[0m";

                return;
            }
        }

        echo $text;
    }

    /**
     * @param bool $bold Whether to make text bold
     */
    public function writeLine(string $text, ?string $color = null, bool $bold = false): void
    {
        $this->write($text.PHP_EOL, $color, $bold);
    }

    public function clear(): void
    {
        $this->system(DIRECTORY_SEPARATOR === '/' ? 'clear' : 'cls');
    }

    public function specialMode(): void
    {
        $this->system('stty -icanon -echo');
    }

    public function normalMode(): void
    {
        $this->system('stty icanon echo');
    }

    public function system(string $command): void
    {
        system($command);
    }

    public function supportsAnsi(): bool
    {
        return DIRECTORY_SEPARATOR === '/'
            || (function_exists('sapi_windows_vt100_support')
            && @sapi_windows_vt100_support(STDOUT));
    }

    /**
     * @return false|resource
     */
    public function inputStream()
    {
        return STDIN;
    }
}
