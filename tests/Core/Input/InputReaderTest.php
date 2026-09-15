<?php

namespace MulerTech\MTerm\Tests\Core\Input;

use MulerTech\MTerm\Core\Input\InputReader;
use MulerTech\MTerm\Core\Input\Key;
use MulerTech\MTerm\Tests\Support\TerminalDouble;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class InputReaderTest extends TestCase
{
    public function testReadLineDropsTheTerminator(): void
    {
        $reader = new InputReader(TerminalDouble::stream("John Doe\nJane\n"));

        $this->assertEquals('John Doe', $reader->readLine());
        $this->assertEquals('Jane', $reader->readLine());
        $this->assertEquals('', $reader->readLine());
    }

    public function testReadCharacterReturnsAWholeAsciiCharacter(): void
    {
        $reader = new InputReader(TerminalDouble::stream('ab'));

        $this->assertEquals('a', $reader->readCharacter());
        $this->assertEquals('b', $reader->readCharacter());
        $this->assertEquals('', $reader->readCharacter());
    }

    /**
     * @return array<string, array{string}>
     */
    public static function multiByteCharacters(): array
    {
        return [
            'two bytes' => ['é'],
            'three bytes' => ['€'],
            'four bytes' => ['🚀'],
        ];
    }

    #[DataProvider('multiByteCharacters')]
    public function testReadCharacterReturnsAWholeMultiByteCharacter(string $character): void
    {
        $reader = new InputReader(TerminalDouble::stream($character.'z'));

        $this->assertEquals($character, $reader->readCharacter());
        $this->assertEquals('z', $reader->readCharacter());
    }

    public function testATruncatedCharacterReturnsWhatArrived(): void
    {
        $reader = new InputReader(TerminalDouble::stream(substr('é', 0, 1)));

        $this->assertEquals(substr('é', 0, 1), $reader->readCharacter());
    }

    public function testAStrayContinuationByteIsReadAlone(): void
    {
        $reader = new InputReader(TerminalDouble::stream("\x80a"));

        $this->assertEquals("\x80", $reader->readCharacter());
        $this->assertEquals('a', $reader->readCharacter());
    }

    /**
     * @return array<string, array{string, Key}>
     */
    public static function keySequences(): array
    {
        return [
            'up' => ["\033[A", Key::Up],
            'down' => ["\033[B", Key::Down],
            'right' => ["\033[C", Key::Right],
            'left' => ["\033[D", Key::Left],
            'home' => ["\033[H", Key::Home],
            'home as a parameter' => ["\033[1~", Key::Home],
            'end' => ["\033[F", Key::End],
            'end as a parameter' => ["\033[4~", Key::End],
            'insert' => ["\033[2~", Key::Insert],
            'delete' => ["\033[3~", Key::Delete],
            'page up' => ["\033[5~", Key::PageUp],
            'page down' => ["\033[6~", Key::PageDown],
            'f1' => ["\033OP", Key::F1],
            'f4' => ["\033OS", Key::F4],
            'f2' => ["\033OQ", Key::F2],
            'f3' => ["\033OR", Key::F3],
            'home as an application key' => ["\033OH", Key::Home],
            'end as an application key' => ["\033OF", Key::End],
            'f5' => ["\033[15~", Key::F5],
            'f6' => ["\033[17~", Key::F6],
            'f7' => ["\033[18~", Key::F7],
            'f8' => ["\033[19~", Key::F8],
            'f9' => ["\033[20~", Key::F9],
            'f10' => ["\033[21~", Key::F10],
            'f11' => ["\033[23~", Key::F11],
            'f12' => ["\033[24~", Key::F12],
            'home as another parameter' => ["\033[7~", Key::Home],
            'end as another parameter' => ["\033[8~", Key::End],
            'enter' => ["\n", Key::Enter],
            'carriage return' => ["\r", Key::Enter],
            'backspace' => ["\x7F", Key::Backspace],
            'backspace as control h' => ["\x08", Key::Backspace],
            'tab' => ["\t", Key::Tab],
            'space' => [' ', Key::Space],
            'escape alone' => ["\033", Key::Escape],
            'unknown sequence' => ["\033[Z", Key::Escape],
            'unknown application key' => ["\033OZ", Key::Escape],
            'escape followed by a letter' => ["\033a", Key::Escape],
        ];
    }

    #[DataProvider('keySequences')]
    public function testReadKeyResolvesASequenceToItsKey(string $sequence, Key $key): void
    {
        $press = (new InputReader(TerminalDouble::stream($sequence)))->readKey();

        $this->assertTrue($press->is($key));
    }

    public function testAnArrowIsReadWholeRatherThanByte(): void
    {
        $reader = new InputReader(TerminalDouble::stream("\033[Bx"));

        $this->assertTrue($reader->readKey()->is(Key::Down));
        $this->assertEquals('x', $reader->readKey()->character);
    }

    public function testSpaceCarriesItsCharacter(): void
    {
        $press = (new InputReader(TerminalDouble::stream(' ')))->readKey();

        $this->assertTrue($press->is(Key::Space));
        $this->assertEquals(' ', $press->character);
        $this->assertTrue($press->isCharacter());
    }

    public function testAnAccentedCharacterComesBackWhole(): void
    {
        $press = (new InputReader(TerminalDouble::stream('é')))->readKey();

        $this->assertNull($press->key);
        $this->assertEquals('é', $press->character);
    }

    public function testAnExhaustedStreamSaysSo(): void
    {
        $press = (new InputReader(TerminalDouble::stream('')))->readKey();

        $this->assertTrue($press->isEndOfInput());
        $this->assertFalse($press->isCharacter());
    }

    public function testATruncatedSequenceEndsOnTheEscapeKey(): void
    {
        $press = (new InputReader(TerminalDouble::stream("\033[")))->readKey();

        $this->assertTrue($press->is(Key::Escape));
    }

    public function testTheStreamIsReadable(): void
    {
        $stream = TerminalDouble::stream('a');

        $this->assertSame($stream, (new InputReader($stream))->stream());
    }

    public function testAnUnusableStreamIsRefused(): void
    {
        $this->expectException(RuntimeException::class);

        new InputReader(false);
    }

    public function testKeysTypedAheadOnATerminalAreDropped(): void
    {
        [$reader, $keyboard] = $this->terminalReader();
        fwrite($keyboard, "y\n");

        $reader->discardPending();
        fwrite($keyboard, 'x');

        $this->assertEquals('x', $reader->readKey()->character);
    }

    public function testNothingTypedAheadLeavesTheNextKeyToBeRead(): void
    {
        [$reader, $keyboard] = $this->terminalReader();

        $reader->discardPending();
        fwrite($keyboard, 'x');

        $this->assertEquals('x', $reader->readKey()->character);
    }

    public function testInputWrittenInAdvanceIsKept(): void
    {
        $reader = new InputReader(TerminalDouble::stream("\nx"));

        $reader->discardPending();

        $this->assertTrue($reader->readKey()->is(Key::Enter));
    }

    public function testAStreamThatCannotBeWaitedUponIsReadOn(): void
    {
        // A memory stream refuses select(), as a Windows console does
        $stream = fopen('php://memory', 'r+b');
        $this->assertIsResource($stream);
        fwrite($stream, "\033[B");
        rewind($stream);

        $this->assertTrue((new InputReader($stream))->readKey()->is(Key::Down));
    }

    /**
     * A reader standing for a terminal, over a socket that holds only what has
     * been written into it — a file would always answer ready.
     *
     * @return array{InputReader, resource}
     */
    private function terminalReader(): array
    {
        $pair = stream_socket_pair(STREAM_PF_UNIX, STREAM_SOCK_STREAM, STREAM_IPPROTO_IP);
        $this->assertIsArray($pair);

        $reader = new class ($pair[0]) extends InputReader {
            protected function isInteractive(): bool
            {
                return true;
            }
        };

        return [$reader, $pair[1]];
    }
}
