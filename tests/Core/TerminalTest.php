<?php

namespace MulerTech\MTerm\Tests\Core;

use InvalidArgumentException;
use MulerTech\MTerm\Core\Color;
use MulerTech\MTerm\Core\Input\Key;
use MulerTech\MTerm\Core\Terminal;
use MulerTech\MTerm\Tests\Support\TerminalDouble;
use PHPUnit\Framework\TestCase;

class TerminalTest extends TestCase
{
    public function testReadReturnsTheLineWithoutItsTerminator(): void
    {
        $double = new TerminalDouble("John Doe\n");

        $this->assertEquals('John Doe', $double->terminal->read('Enter name: '));
        $this->assertEquals('Enter name: ', $double->display());
    }

    public function testReadCharReturnsAWholeCharacter(): void
    {
        $double = new TerminalDouble('éa');

        $this->assertEquals('é', $double->terminal->readChar('Key: '));
        $this->assertEquals('a', $double->terminal->readChar());
        $this->assertEquals('Key: ', $double->display());
    }

    public function testReadKeyResolvesAnArrow(): void
    {
        $double = new TerminalDouble("\033[A");

        $this->assertTrue($double->terminal->readKey('Move: ')->is(Key::Up));
        $this->assertEquals('Move: ', $double->display());
    }

    public function testWriteWithAnsiSupport(): void
    {
        $double = new TerminalDouble('', true);
        $double->terminal->write('Test', Color::Green);

        $this->assertEquals("\033[0;32mTest\033[0m", $double->display());
    }

    public function testWriteInBoldWithAnsiSupport(): void
    {
        $double = new TerminalDouble('', true);
        $double->terminal->write('Test', Color::Green, true);

        $this->assertEquals("\033[1;32mTest\033[0m", $double->display());
    }

    public function testWriteWithoutAnsiSupportLeavesNoSequence(): void
    {
        $double = new TerminalDouble();
        $double->terminal->write('Test', Color::Green, true);

        $this->assertEquals('Test', $double->display());
    }

    public function testWriteWithoutColorLeavesNoSequence(): void
    {
        $double = new TerminalDouble('', true);
        $double->terminal->write('Test');

        $this->assertEquals('Test', $double->display());
    }

    public function testWriteLineAppendsTheLineBreak(): void
    {
        $double = new TerminalDouble('', true);
        $double->terminal->writeLine('Test', Color::Green);

        $this->assertEquals("\033[0;32mTest".PHP_EOL."\033[0m", $double->display());
    }

    public function testWriteLineWithoutArgumentBreaksTheLine(): void
    {
        $double = new TerminalDouble();
        $double->terminal->writeLine();

        $this->assertEquals(PHP_EOL, $double->display());
    }

    public function testClearErasesTheScreenAndItsScrollbackWithoutASubprocess(): void
    {
        $double = new TerminalDouble('', true);
        $double->terminal->clear();

        $this->assertEquals("\033[H\033[2J\033[3J", $double->display());
    }

    public function testClearLineReturnsToTheFirstColumn(): void
    {
        $double = new TerminalDouble('', true);
        $double->terminal->clearLine();

        $this->assertEquals("\r\033[2K", $double->display());
    }

    public function testMoveCursorPlacesItAtTheGivenCoordinates(): void
    {
        $double = new TerminalDouble('', true);
        $double->terminal->moveCursor(3, 12);

        $this->assertEquals("\033[3;12H", $double->display());
    }

    public function testCoordinatesBelowOneAreRefused(): void
    {
        $double = new TerminalDouble('', true);

        $this->expectException(InvalidArgumentException::class);

        $double->terminal->moveCursor(0, 1);
    }

    public function testCursorVisibilityIsDriven(): void
    {
        $double = new TerminalDouble('', true);
        $double->terminal->hideCursor();
        $double->terminal->showCursor();

        $this->assertEquals("\033[?25l\033[?25h", $double->display());
    }

    public function testARedirectedDisplayCarriesNoSequence(): void
    {
        $double = new TerminalDouble();
        $double->terminal->clear();
        $double->terminal->clearLine();
        $double->terminal->moveCursor(2, 2);
        $double->terminal->hideCursor();
        $double->terminal->showCursor();
        $double->terminal->writeLine('Report', Color::Red, true);

        $this->assertEquals('Report'.PHP_EOL, $double->display());
        $this->assertStringNotContainsString("\033", $double->display());
    }

    public function testRawModeIsEnabledAndRestored(): void
    {
        $double = new TerminalDouble();

        $this->assertFalse($double->terminal->isRawMode());

        $double->terminal->enableRawMode();
        $this->assertTrue($double->terminal->isRawMode());

        $double->terminal->disableRawMode();
        $this->assertFalse($double->terminal->isRawMode());
        $this->assertEquals(['-g', '-icanon -echo min 1 time 0', 'saved-state'], $double->mode->calls);
    }

    public function testSupportsAnsiFollowsTheOutput(): void
    {
        $this->assertTrue((new TerminalDouble('', true))->terminal->supportsAnsi());
        $this->assertFalse((new TerminalDouble())->terminal->supportsAnsi());
    }

    public function testTheOutputAndTheInputAreReachable(): void
    {
        $double = new TerminalDouble('a');

        $this->assertSame($double->output, $double->terminal->getOutput());
        $this->assertEquals('a', $double->terminal->getInput()->readCharacter());
    }

    public function testATerminalBuiltWithoutArgumentsUsesTheStandardStreams(): void
    {
        $terminal = new Terminal();

        $this->assertIsBool($terminal->supportsAnsi());
    }
}
