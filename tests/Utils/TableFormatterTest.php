<?php

namespace MulerTech\MTerm\Tests\Utils;

use MulerTech\MTerm\Utils\ColorOutput;
use MulerTech\MTerm\Utils\TableFormatter;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;

class TableFormatterTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testRenderTable(): void
    {
        $mockOutput = $this->createMock(ColorOutput::class);

        // Expected sequence of calls to the mock
        $mockOutput->expects($this->exactly(3))
            ->method('writeLineColored');

        $mockOutput->expects($this->exactly(24))
            ->method('writeColored');

        $mockOutput->expects($this->exactly(3))
            ->method('writeLine');

        $tableFormatter = new TableFormatter($mockOutput);

        $headers = ['ID', 'Name', 'Email'];
        $rows = [
            [1, 'John Doe', 'john@example.com'],
            [2, 'Jane Smith', 'jane@example.com']
        ];

        $tableFormatter->renderTable($headers, $rows);
    }

    /**
     * @throws \ReflectionException
     * @throws Exception
     */
    public function testCalculateColumnWidths(): void
    {
        $mockOutput = $this->createMock(ColorOutput::class);
        $tableFormatter = new TableFormatter($mockOutput);

        $reflection = new \ReflectionClass($tableFormatter);
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