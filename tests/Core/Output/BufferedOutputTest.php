<?php

namespace MulerTech\MTerm\Tests\Core\Output;

use MulerTech\MTerm\Core\Output\BufferedOutput;
use PHPUnit\Framework\TestCase;

class BufferedOutputTest extends TestCase
{
    public function testContentKeepsEverythingWritten(): void
    {
        $output = new BufferedOutput();
        $output->write('one ');
        $output->write('two');

        $this->assertEquals('one two', $output->content());
        $this->assertEquals('one two', $output->content());
    }

    public function testFetchEmptiesTheBuffer(): void
    {
        $output = new BufferedOutput();
        $output->write('once');

        $this->assertEquals('once', $output->fetch());
        $this->assertEquals('', $output->content());
    }

    public function testDecorationIsPlainByDefault(): void
    {
        $this->assertFalse((new BufferedOutput())->isDecorated());
        $this->assertTrue((new BufferedOutput(true))->isDecorated());
    }
}
