<?php

namespace MulerTech\MTerm\Tests\Utils;

use MulerTech\MTerm\Utils\ColorOutput;
use PHPUnit\Framework\TestCase;

class ColorOutputTest extends TestCase
{
    private ColorOutput $output;

    protected function setUp(): void
    {
        $this->output = new ColorOutput();
    }

    public function testWriteColored(): void
    {
        ob_start();
        $this->output->writeColored('Test message', ColorOutput::RED);
        $result = ob_get_clean();

        if ($this->supportsAnsi()) {
            $this->assertEquals("\033[0;31mTest message\033[0m", $result);
        } else {
            $this->assertEquals('Test message', $result);
        }
    }

    public function testWriteLineColored(): void
    {
        ob_start();
        $this->output->writeLineColored('Test message', ColorOutput::GREEN);
        $result = ob_get_clean();

        if ($this->supportsAnsi()) {
            $this->assertEquals("\033[0;32mTest message\033[0m" . PHP_EOL, $result);
        } else {
            $this->assertEquals('Test message' . PHP_EOL, $result);
        }
    }

    public function testBoldText(): void
    {
        ob_start();
        $this->output->writeColored('Test message', ColorOutput::BLUE, true);
        $result = ob_get_clean();

        if ($this->supportsAnsi()) {
            $this->assertEquals("\033[1;34mTest message\033[0m", $result);
        } else {
            $this->assertEquals('Test message', $result);
        }
    }

    private function supportsAnsi(): bool
    {
        // Simple check for testing purposes
        return getenv('TERM') !== false && getenv('TERM') !== 'dumb';
    }
}