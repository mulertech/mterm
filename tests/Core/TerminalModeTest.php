<?php

namespace MulerTech\MTerm\Tests\Core;

use MulerTech\MTerm\Core\TerminalMode;
use MulerTech\MTerm\Tests\Support\RecordingTerminalMode;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class TerminalModeTest extends TestCase
{
    public function testEnablingRawModeSavesTheStateFirst(): void
    {
        $mode = new RecordingTerminalMode();
        $mode->enableRaw();

        $this->assertTrue($mode->isRaw());
        $this->assertEquals(['-g', '-icanon -echo min 1 time 0'], $mode->calls);
    }

    public function testRestoringPutsTheSavedStateBack(): void
    {
        $mode = new RecordingTerminalMode();
        $mode->enableRaw();
        $mode->restore();

        $this->assertFalse($mode->isRaw());
        $this->assertEquals(['-g', '-icanon -echo min 1 time 0', 'saved-state'], $mode->calls);
    }

    public function testRestoringTwiceChangesNothing(): void
    {
        $mode = new RecordingTerminalMode();
        $mode->enableRaw();
        $mode->restore();
        $mode->restore();

        $this->assertCount(3, $mode->calls);
    }

    public function testEnablingTwiceKeepsTheFirstSavedState(): void
    {
        $mode = new RecordingTerminalMode();
        $mode->enableRaw();
        $mode->enableRaw();

        $this->assertCount(2, $mode->calls);
    }

    public function testWithoutATerminalThereIsNoModeToChange(): void
    {
        $mode = new RecordingTerminalMode(false);
        $mode->enableRaw();

        $this->assertFalse($mode->isRaw());
        $this->assertSame([], $mode->calls);
    }

    public function testAStateThatCannotBeReadRefusesRawMode(): void
    {
        $mode = new RecordingTerminalMode(true, null);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('would not be reversible');

        $mode->enableRaw();
    }

    public function testAnEmptyStateRefusesRawMode(): void
    {
        $mode = new RecordingTerminalMode(true, '  ');

        $this->expectException(RuntimeException::class);

        $mode->enableRaw();
    }

    public function testTheModeInspectsTheTerminalItRunsOn(): void
    {
        $mode = new TerminalMode();

        $this->assertIsBool((new \ReflectionMethod($mode, 'hasTerminal'))->invoke($mode));
    }

    public function testSttyIsAskedForTheCurrentState(): void
    {
        $mode = new TerminalMode();
        $state = (new \ReflectionMethod($mode, 'runStty'))->invoke($mode, '-g');

        $this->assertTrue(null === $state || is_string($state));
    }

    public function testOnlyTheSignalsThePlatformDefinesAreWatched(): void
    {
        $mode = new TerminalMode();
        $signals = (new \ReflectionMethod($mode, 'interruptionSignals'))->invoke($mode);

        $this->assertIsArray($signals);
        $this->assertSame($signals, array_filter($signals, is_int(...)));
    }
}
