<?php

namespace MulerTech\MTerm\Tests\Core;

use MulerTech\MTerm\Core\Terminal;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionException;

class TerminalTest extends TestCase
{
    public function testRead(): void
    {
        $file = fopen(__DIR__ . DIRECTORY_SEPARATOR . 'input', 'r');

        $terminal = $this->getMockBuilder(Terminal::class)
            ->onlyMethods(['inputStream'])
            ->getMock();

        $terminal->expects($this->once())
            ->method('inputStream')
            ->willReturn($file);

        $this->assertEquals('John Doe', $terminal->read('Enter name: '));
    }

    public function testReadChar(): void
    {
        $file = fopen(__DIR__ . DIRECTORY_SEPARATOR . 'input', 'r');

        $terminal = $this->getMockBuilder(Terminal::class)
            ->onlyMethods(['inputStream'])
            ->getMock();

        $terminal->expects($this->once())
            ->method('inputStream')
            ->willReturn($file);

        $this->assertEquals('J', $terminal->readChar('Enter name: '));
    }

    public function testWriteWithAnsiSupportRealCase(): void
    {
        // Create a mock that will force supportsAnsi() to return false
        $terminal = $this->getMockBuilder(Terminal::class)
            ->onlyMethods(['supportsAnsi'])
            ->getMock();

        $terminal->method('supportsAnsi')
            ->willReturn(true);

        ob_start();
        $terminal->write('Test', 'green');
        $output = ob_get_clean();

        $this->assertEquals('[0;32mTest[0m', $output);
    }

    public function testWriteWithoutAnsiSupportRealCase(): void
    {
        // Create a mock that will force supportsAnsi() to return false
        $terminal = $this->getMockBuilder(Terminal::class)
            ->onlyMethods(['supportsAnsi'])
            ->getMock();

        $terminal->method('supportsAnsi')
            ->willReturn(false);

        ob_start();
        $terminal->write('Test', 'green');
        $output = ob_get_clean();

        $this->assertEquals('Test', $output);
    }

    public function testWriteLineWithAnsiSupportRealCase(): void
    {
        // Create a mock that will force supportsAnsi() to return false
        $terminal = $this->getMockBuilder(Terminal::class)
            ->onlyMethods(['supportsAnsi'])
            ->getMock();

        $terminal->method('supportsAnsi')
            ->willReturn(true);

        ob_start();
        $terminal->writeLine('Test', 'green');
        $output = ob_get_clean();

        $this->assertEquals('[0;32mTest' . PHP_EOL . '[0m', $output);
    }

    public function testWriteLineWithoutAnsiSupportRealCase(): void
    {
        // Create a mock that will force supportsAnsi() to return false
        $terminal = $this->getMockBuilder(Terminal::class)
            ->onlyMethods(['supportsAnsi'])
            ->getMock();

        $terminal->method('supportsAnsi')
            ->willReturn(false);

        ob_start();
        $terminal->writeLine('Test', 'green');
        $output = ob_get_clean();

        $this->assertEquals('Test' . PHP_EOL, $output);
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

    public function testSpecialMode(): void
    {
        // Mock pour éviter d'appeler system()
        $terminal = $this->getMockBuilder(Terminal::class)
            ->onlyMethods(['system'])
            ->getMock();

        $terminal->expects($this->once())
            ->method('system')
            ->with('stty -icanon -echo');

        $terminal->specialMode();
    }

    public function testNormalMode(): void
    {
        // Mock pour éviter d'appeler system()
        $terminal = $this->getMockBuilder(Terminal::class)
            ->onlyMethods(['system'])
            ->getMock();

        $terminal->expects($this->once())
            ->method('system')
            ->with('stty icanon echo');

        $terminal->normalMode();
    }

    /**
     * @throws ReflectionException
     */
    public function testSystem(): void
    {
        $terminal = new Terminal();
        $reflection = new ReflectionClass(Terminal::class);
        $method = $reflection->getMethod('system');

        ob_start();
        $method->invoke($terminal, 'echo "Hello"');
        $output = ob_get_clean();

        $this->assertEquals('Hello' . PHP_EOL, $output);
    }

    public function testSupportsAnsi(): void
    {
        $terminal = new Terminal();
        $this->assertIsBool($terminal->supportsAnsi());
    }

    /**
     * @throws ReflectionException
     */
    public function testInputStream(): void
    {
        $terminal = new Terminal();
        $reflection = new ReflectionClass(Terminal::class);
        $method = $reflection->getMethod('inputStream');

        $this->assertIsResource($method->invoke($terminal));
    }
}