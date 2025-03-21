<?php

namespace MulerTech\MTerm\Tests\Utils;

use MulerTech\MTerm\Core\Terminal;
use MulerTech\MTerm\Utils\TableFormatter;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionException;

class TableFormatterTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testRenderTable(): void
    {
        $mockTerminal = $this->createMock(Terminal::class);

        // Expected sequence of calls to the mock
        $mockTerminal->expects($this->exactly(6))
            ->method('writeLine');

        $mockTerminal->expects($this->exactly(24))
            ->method('write');

        $tableFormatter = new TableFormatter($mockTerminal);

        $headers = ['ID', 'Name', 'Email'];
        $rows = [
            [1, 'John Doe', 'john@example.com'],
            [2, 'Jane Smith', 'jane@example.com']
        ];

        $tableFormatter->renderTable($headers, $rows);
    }

    /**
     * @throws ReflectionException
     * @throws Exception
     */
    public function testCalculateColumnWidths(): void
    {
        $mockTerminal = $this->createMock(Terminal::class);
        $tableFormatter = new TableFormatter($mockTerminal);

        $reflection = new ReflectionClass($tableFormatter);
        $method = $reflection->getMethod('calculateColumnWidths');

        $headers = ['ID', 'Name', 'Email'];
        $rows = [
            [1, 'John Doe', 'john@example.com'],
            [2, 'Jane Smith', 'jane@example.com']
        ];

        $result = $method->invoke($tableFormatter, $headers, $rows);

        // Headers width + padding (2)
        $this->assertEquals([4, 12, 18], $result);
    }
}