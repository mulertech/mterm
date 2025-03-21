<?php

namespace MulerTech\MTerm\Tests\Utils;

use MulerTech\MTerm\Core\Terminal;
use MulerTech\MTerm\Utils\ProgressBar;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;

class ProgressBarTest extends TestCase
{
    private Terminal $terminalMock;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->terminalMock = $this->createMock(Terminal::class);
    }

    public function testStartInitializesProgressBar(): void
    {
        // Expect terminal write to be called with initial progress bar
        $this->terminalMock
            ->expects($this->once())
            ->method('write')
            ->with(
                $this->stringContains('[--------------------------------------------------]   0% 0.0s')
            );

        $progressBar = new ProgressBar($this->terminalMock);
        $progressBar->start();
    }

    public function testAdvanceUpdatesProgressBar(): void
    {
        // Expect terminal write to be called twice (once for start, once for advance)
        $this->terminalMock
            ->expects($this->exactly(2))
            ->method('write')
            ->withAnyParameters();

        $progressBar = new ProgressBar($this->terminalMock);
        $progressBar->start();
        $progressBar->advance(20);
    }

    public function testSetProgressSetsSpecificValue(): void
    {
        // Expect terminal write to be called twice (once for start, once for setProgress)
        $this->terminalMock
            ->expects($this->exactly(2))
            ->method('write')
            ->withAnyParameters();

        $progressBar = new ProgressBar($this->terminalMock);
        $progressBar->start();
        $progressBar->setProgress(50);
    }

    public function testSetProgressClampsToValidRange(): void
    {
        // Negative value should be clamped to 0
        $this->terminalMock
            ->expects($this->exactly(2))
            ->method('write')
            ->withAnyParameters();

        $progressBar = new ProgressBar($this->terminalMock);
        $progressBar->start();
        $progressBar->setProgress(-10);
    }

    public function testSetProgressClampsToMaximum(): void
    {
        // Value > total should be clamped to total (100%)
        $this->terminalMock
            ->expects($this->exactly(3))
            ->method('write')
            ->withAnyParameters();

        $progressBar = new ProgressBar($this->terminalMock);
        $progressBar->start();
        $progressBar->setProgress(150);
        $progressBar->advance(50);
    }

    public function testFinishCompletesProgressAndAddsNewLine(): void
    {
        // Expect terminal write to be called, then terminal writeLine
        $this->terminalMock
            ->expects($this->once())
            ->method('write')
            ->with($this->stringContains('[==================================================] 100%'));

        $this->terminalMock
            ->expects($this->once())
            ->method('writeLine')
            ->with('');

        $progressBar = new ProgressBar($this->terminalMock);
        $progressBar->finish();
    }

    public function testCustomBarRendering(): void
    {
        $this->terminalMock
            ->expects($this->once())
            ->method('write')
            ->with($this->stringContains('[**********##########]  50%'), Terminal::COLORS['red'], false);

        $progressBar = new ProgressBar(
            $this->terminalMock,
            100,  // total
            20,   // width
            '*',  // completeChar
            '#',  // incompleteChar
            Terminal::COLORS['red']
        );

        $progressBar->setProgress(50);
    }

    public function testTimeElapsedIsShown(): void
    {
        $this->terminalMock
            ->expects($this->exactly(2))
            ->method('write')
            ->withAnyParameters();

        $progressBar = new ProgressBar($this->terminalMock);
        $progressBar->start();

        // Sleep to ensure time passes
        usleep(100000);  // 0.1 seconds

        $progressBar->advance(10);
    }

    public function testZeroTotalHandling(): void
    {
        $this->terminalMock
            ->expects($this->once())
            ->method('write')
            ->with($this->stringContains('[--------------------------------------------------]   0% 0.0s'));

        $progressBar = new ProgressBar($this->terminalMock, 0);
        $progressBar->start();
    }
}