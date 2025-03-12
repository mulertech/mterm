<?php

namespace MulerTech\MTerm\Tests\Utils;

use MulerTech\MTerm\Utils\Output;
use PHPUnit\Framework\TestCase;

class OutputTest extends TestCase
{
    public function testWrite(): void
    {
        $output = new Output();

        ob_start();
        $output->write('Test message');
        $result = ob_get_clean();

        $this->assertEquals('Test message', $result);
    }

    public function testWriteLine(): void
    {
        $output = new Output();

        ob_start();
        $output->writeLine('Test message');
        $result = ob_get_clean();

        $this->assertEquals('Test message' . PHP_EOL, $result);
    }

    public function testIsWindows(): void
    {
        $output = new Output();
        $reflection = new \ReflectionClass($output);
        $method = $reflection->getMethod('isWindows');
        $method->setAccessible(true);

        $this->assertSame(DIRECTORY_SEPARATOR === '\\', $method->invoke($output));
    }
}