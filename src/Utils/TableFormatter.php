<?php

namespace MulerTech\MTerm\Utils;

use MulerTech\MTerm\Core\Terminal;

/**
 * Class TableFormatter
 * @package MulerTech\MTerm
 * @author Sébastien Muler
 */
class TableFormatter
{
    private Terminal $terminal;
    private string $headerColor;
    private string $borderColor;
    private string $cellColor;
    private int $padding;

    /**
     * @param Terminal $terminal
     * @param string $headerColor
     * @param string $borderColor
     * @param string $cellColor
     * @param int $padding
     */
    public function __construct(
        Terminal $terminal,
        string $headerColor = Terminal::COLORS['green'],
        string $borderColor = Terminal::COLORS['blue'],
        string $cellColor = Terminal::COLORS['white'],
        int $padding = 1
    ) {
        $this->terminal = $terminal;
        $this->headerColor = $headerColor;
        $this->borderColor = $borderColor;
        $this->cellColor = $cellColor;
        $this->padding = $padding;
    }

    /**
     * Format and terminal a table
     *
     * @param array<int|string, string> $headers Table headers
     * @param array<int, array<int|string, string|int|float>> $rows Table data rows
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
     * @param array<int|string, string> $headers Table headers
     * @param array<int, array<int|string, string|int|float>> $rows Table data rows
     * @return array<int, int> Array of column widths
     */
    private function calculateColumnWidths(array $headers, array $rows): array
    {
        $widths = [];
        $i = 0;

        // Utiliser des indices entiers explicites
        foreach ($headers as $header) {
            $widths[$i] = strlen($header);
            $i++;
        }

        foreach ($rows as $row) {
            $i = 0;
            foreach ($row as $cell) {
                $cellLength = strlen((string)$cell);
                $widths[$i] = max($widths[$i] ?? 0, $cellLength);
                $i++;
            }
        }

        // Add padding
        return array_map(fn ($width) => $width + ($this->padding * 2), $widths);
    }

    /**
     * Draw horizontal separator line
     *
     * @param array<int, int> $columnWidths Array of column widths
     * @return void
     */
    private function drawSeparator(array $columnWidths): void
    {
        $line = '+';
        foreach ($columnWidths as $width) {
            $line .= str_repeat('-', $width) . '+';
        }
        $this->terminal->writeLine($line, $this->borderColor);
    }

    /**
     * Draw a table row
     *
     * @param array<int|string, string|int|float> $row Row data
     * @param array<int, int> $columnWidths Array of column widths
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

        $this->terminal->write('|', $this->borderColor);

        $parts = explode('|', $line);
        array_shift($parts); // Remove first empty element

        foreach ($parts as $i => $part) {
            if ($i > 0) {
                $this->terminal->write('|', $this->borderColor);
            }
            $this->terminal->write($part, $color, $bold);
        }

        $this->terminal->writeLine('');
    }
}
