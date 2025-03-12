<?php

namespace MulerTech\MTerm\Utils;

class ColorOutput extends Output
{
    public const BLACK = 'black';
    public const RED = 'red';
    public const GREEN = 'green';
    public const YELLOW = 'yellow';
    public const BLUE = 'blue';
    public const MAGENTA = 'magenta';
    public const CYAN = 'cyan';
    public const WHITE = 'white';

    private const ANSI_CODES = [
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

    /**
     * Output colored text
     *
     * @param string $text Text to output
     * @param string $color Color name (use ColorOutput constants)
     * @param bool $bold Whether to make text bold
     * @return void
     */
    public function writeColored(string $text, string $color, bool $bold = false): void
    {
        if ($this->supportsAnsi()) {
            $colorKey = $bold ? "bold_{$color}" : $color;
            $code = self::ANSI_CODES[$colorKey] ?? self::ANSI_CODES['white'];
            $this->write("\033[{$code}m{$text}\033[0m");
        } else {
            $this->write($text);
        }
    }

    /**
     * Output colored text with newline
     *
     * @param string $text Text to output
     * @param string $color Color name (use ColorOutput constants)
     * @param bool $bold Whether to make text bold
     * @return void
     */
    public function writeLineColored(string $text, string $color, bool $bold = false): void
    {
        $this->writeColored($text, $color, $bold);
        $this->write(PHP_EOL);
    }

    /**
     * Check if terminal supports ANSI color codes
     *
     * @return bool
     */
    private function supportsAnsi(): bool
    {
        if ($this->isWindows()) {
            return function_exists('sapi_windows_vt100_support') &&
                @sapi_windows_vt100_support(STDOUT);
        }

        return true;
    }
}