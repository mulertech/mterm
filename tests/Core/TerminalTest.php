<?php

namespace MulerTech\MTerm\Tests\Core;

use MulerTech\MTerm\Core\Terminal;
use PHPUnit\Framework\TestCase;

class TerminalTest extends TestCase
{
    private Terminal $terminal;

    protected function setUp(): void
    {
        $this->terminal = new Terminal();
    }

    public function testWriteWithoutColor(): void
    {
        ob_start();
        $this->terminal->write('Hello World');
        $output = ob_get_clean();

        $this->assertEquals('Hello World', $output);
    }

    public function testWriteWithColor(): void
    {
        $terminal = new Terminal();

        ob_start();
        $terminal->write('Error', 'red');
        $output = ob_get_clean();

        $this->assertEquals("\033[0;31mError\033[0m", $output);
    }

    public function testWriteLineWithoutColor(): void
    {
        ob_start();
        $this->terminal->writeLine('Hello World');
        $output = ob_get_clean();

        $this->assertEquals('Hello World' . PHP_EOL, $output);
    }

    public function testWriteLineWithColor(): void
    {
        $terminal = new Terminal();

        ob_start();
        $terminal->writeLine('Success', 'green');
        $output = ob_get_clean();

        $this->assertEquals("\033[0;32mSuccess" . PHP_EOL . "\033[0m", $output);
    }

    public function testClearOnAnsiTerminalAndElse(): void
    {
        $terminal = new Terminal();

        if (DIRECTORY_SEPARATOR === '/' ||
            (function_exists('sapi_windows_vt100_support') &&
                @sapi_windows_vt100_support(STDOUT))) {
            $result = "[H[J";
        } else {
            $result = str_repeat(PHP_EOL, 50);
        }

        ob_start();
        $terminal->clear();
        $output = ob_get_clean();

        $this->assertEquals($result, $output);
    }

    public function testColorFallsBackWhenColorNotSupported(): void
    {
        ob_start();
        $this->terminal->write('Test', 'invalid_color');
        $output = ob_get_clean();

        $this->assertEquals('Test', $output);
    }
}