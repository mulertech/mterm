<?php

namespace MulerTech\MTerm\Tests\Core\Output;

use MulerTech\MTerm\Core\Output\StreamOutput;
use MulerTech\MTerm\Tests\Support\TerminalDouble;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class StreamOutputTest extends TestCase
{
    public function testWriteReachesTheStream(): void
    {
        $stream = TerminalDouble::stream('');
        $output = new StreamOutput($stream);

        $output->write('written');
        rewind($stream);

        $this->assertEquals('written', stream_get_contents($stream));
    }

    public function testDecorationCanBeForced(): void
    {
        $stream = TerminalDouble::stream('');

        $this->assertTrue((new StreamOutput($stream, true))->isDecorated());
        $this->assertFalse((new StreamOutput($stream, false))->isDecorated());
    }

    public function testAStreamThatIsNotATerminalStaysPlain(): void
    {
        $output = new StreamOutput(TerminalDouble::stream(''));

        $this->assertFalse($output->isDecorated());
    }

    public function testNoColorKeepsTheOutputPlain(): void
    {
        $previous = getenv('NO_COLOR');
        putenv('NO_COLOR=1');

        try {
            $this->assertFalse((new StreamOutput(TerminalDouble::stream('')))->isDecorated());
        } finally {
            false === $previous ? putenv('NO_COLOR') : putenv('NO_COLOR='.$previous);
        }
    }

    public function testAnUnusableStreamIsRefused(): void
    {
        $this->expectException(RuntimeException::class);

        new StreamOutput(false);
    }
}
