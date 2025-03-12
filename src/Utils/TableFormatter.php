<?php

namespace MulerTech\MTerm\Utils;

class TableFormatter
{
    private ColorOutput $output;
    private string $headerColor;
    private string $borderColor;
    private string $cellColor;
    private int $padding;

    public function __construct(
        ColorOutput $output,
        string $headerColor = ColorOutput::GREEN,
        string $borderColor = ColorOutput::BLUE,
        string $cellColor = ColorOutput::WHITE,
        int $padding = 1
    ) {
        $this->output = $output;
        $this->headerColor = $headerColor;
        $this->borderColor = $borderColor;
        $this->cellColor = $cellColor;
        $this->padding = $padding;
    }

    /**
     * Format and output a table
     *
     * @param array $headers Table headers
     * @param array $rows Table data rows
     * @return void
     */
    public function renderTable(array $headers, array $rows): void
    {
        $columnWidths = $this->calculateColumnWidths($headers, $rows);

        $this->drawSeparator($columnWidths);
        $this->drawRow($headers, $columnWidths, $this->headerColor, true);
        $this->drawSeparator($columnWidths);

        foreach ($rows as $row) {
            $this->drawRow($row, $columnWidths, $this->cellColor);
        }

        $this->drawSeparator($columnWidths);
    }

    /**
     * Calculate the width of each column
     *
     * @param array $headers Table headers
     * @param array $rows Table data rows
     * @return array Array of column widths
     */
    private function calculateColumnWidths(array $headers, array $rows): array
    {
        $widths = array_map('strlen', $headers);

        foreach ($rows as $row) {
            $i = 0;
            foreach ($row as $cell) {
                $cellLength = strlen((string)$cell);
                $widths[$i] = max($widths[$i] ?? 0, $cellLength);
                $i++;
            }
        }

        // Add padding
        return array_map(fn($width) => $width + ($this->padding * 2), $widths);
    }

    /**
     * Draw horizontal separator line
     *
     * @param array $columnWidths Array of column widths
     * @return void
     */
    private function drawSeparator(array $columnWidths): void
    {
        $line = '+';
        foreach ($columnWidths as $width) {
            $line .= str_repeat('-', $width) . '+';
        }
        $this->output->writeLineColored($line, $this->borderColor);
    }

    /**
     * Draw a table row
     *
     * @param array $row Row data
     * @param array $columnWidths Array of column widths
     * @param string $color Color for the row
     * @param bool $bold Whether to make text bold
     * @return void
     */
    private function drawRow(array $row, array $columnWidths, string $color, bool $bold = false): void
    {
        $line = '|';
        $i = 0;
        foreach ($row as $cell) {
            $width = $columnWidths[$i] ?? 10;
            $padding = $this->padding;
            $cellContent = str_pad((string)$cell, $width - $padding * 2, ' ');
            $line .= str_repeat(' ', $padding) . $cellContent . str_repeat(' ', $padding) . '|';
            $i++;
        }

        $this->output->writeColored('|', $this->borderColor);

        $parts = explode('|', $line);
        array_shift($parts); // Remove first empty element

        foreach ($parts as $i => $part) {
            if ($i > 0) {
                $this->output->writeColored('|', $this->borderColor);
            }
            $this->output->writeColored($part, $color, $bold);
        }

        $this->output->writeLine('');
    }
}