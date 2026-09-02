<?php

namespace MulerTech\MTerm\Ui;

use MulerTech\MTerm\Core\Terminal;

/**
 * Displays indicators on a common alignment, remedies in their own column.
 *
 * @author Sébastien Muler
 */
class IndicatorRenderer
{
    private const string REMEDY_MARKER = '→ ';

    public function __construct(private readonly Terminal $terminal)
    {
    }

    public function render(Indicator ...$indicators): void
    {
        $labelWidth = 0;

        foreach ($indicators as $indicator) {
            $labelWidth = max($labelWidth, mb_strwidth($indicator->label));
        }

        foreach ($indicators as $indicator) {
            $this->renderOne($indicator, $labelWidth);
        }
    }

    private function renderOne(Indicator $indicator, int $labelWidth): void
    {
        $hasRemedy = null !== $indicator->remedy && '' !== $indicator->remedy;
        $label = $hasRemedy ? $this->pad($indicator->label, $labelWidth) : $indicator->label;

        $this->terminal->write($indicator->status->symbol().' '.$label, $indicator->status->color());

        if (!$hasRemedy) {
            $this->terminal->writeLine();

            return;
        }

        $this->terminal->writeLine('  '.self::REMEDY_MARKER.$indicator->remedy);
    }

    /**
     * Pad to a display width rather than a byte count, so accents and symbols
     * keep the remedy column aligned.
     */
    private function pad(string $text, int $width): string
    {
        return $text.str_repeat(' ', max(0, $width - mb_strwidth($text)));
    }
}
