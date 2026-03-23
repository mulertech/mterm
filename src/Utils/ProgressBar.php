<?php

namespace MulerTech\MTerm\Utils;

use MulerTech\MTerm\Core\Terminal;

/**
 * Class ProgressBar.
 *
 * @author Sébastien Muler
 */
class ProgressBar
{
    private Terminal $terminal;
    private int $total;
    private int $current;
    private int $width;
    private string $completeChar;
    private string $incompleteChar;
    private string $color;
    private ?float $startTime = null;

    public function __construct(
        Terminal $terminal,
        int $total = 100,
        int $width = 50,
        string $completeChar = '=',
        string $incompleteChar = '-',
        string $color = Terminal::COLORS['green'],
    ) {
        $this->terminal = $terminal;
        $this->total = $total;
        $this->width = $width;
        $this->completeChar = $completeChar;
        $this->incompleteChar = $incompleteChar;
        $this->color = $color;
        $this->current = 0;
    }

    /**
     * Start the progress bar.
     */
    public function start(): void
    {
        $this->startTime = microtime(true);
        $this->current = 0;
        $this->draw();
    }

    /**
     * Advance the progress bar by a specific amount.
     *
     * @param int $step Amount to advance
     */
    public function advance(int $step = 1): void
    {
        $this->current += $step;
        if ($this->current > $this->total) {
            $this->current = $this->total;
        }
        $this->draw();
    }

    /**
     * Set the progress to a specific value.
     *
     * @param int $current New progress value
     */
    public function setProgress(int $current): void
    {
        $this->current = max(0, min($current, $this->total));
        $this->draw();
    }

    /**
     * Finish the progress bar.
     */
    public function finish(): void
    {
        $this->current = $this->total;
        $this->draw();
        $this->terminal->writeLine('');
    }

    /**
     * Draw the progress bar.
     */
    private function draw(): void
    {
        $percent = $this->total > 0 ? floor(($this->current / $this->total) * 100) : 0;
        $filledWidth = $this->total > 0 ? floor(($this->current / $this->total) * $this->width) : 0;

        $bar = str_repeat($this->completeChar, (int) $filledWidth);
        $bar .= str_repeat($this->incompleteChar, $this->width - (int) $filledWidth);

        $timeInfo = '';
        if (null !== $this->startTime) {
            $elapsedTime = microtime(true) - $this->startTime;
            $timeInfo = sprintf(' %.1fs', $elapsedTime);
        }

        $line = sprintf("\r[%s] %3d%%%s", $bar, $percent, $timeInfo);

        $this->terminal->write($line, $this->color);
    }
}
