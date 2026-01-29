<?php

declare(strict_types=1);

namespace Rovers;

/**
 * Draws the terrain grid with ASCII box-drawing characters.
 * Each cell is 4 characters wide, 1 tall. R = rover, S = scent, RS = both (lost rovers not shown).
 */
final class GridVisualiser
{
    private const CELL_WIDTH = 4;

    /** @var string[] Box-drawing: top-left, top-T, top-right, left-T, cross, right-T, bottom-left, bottom-T, bottom-right, vertical */
    private const BOX = [
        'top_left'      => '┌',
        'top_T'         => '┬',
        'top_right'     => '┐',
        'left_T'        => '├',
        'cross_bar'     => '┼',
        'right_T'       => '┤',
        'bottom_left'   => '└',
        'bottom_T'      => '┴',
        'bottom_right'  => '┘',
        'wall'          => '│',
        'line'          => '─',
    ];

    /**
     * @param int $maxX Upper-right X (grid columns 0..maxX)
     * @param int $maxY Upper-right Y (grid rows 0..maxY)
     * @param list<array{0: int, 1: int}> $roverPositions Final positions of non-lost rovers [[x,y], ...]
     * @param list<string> $scentCells Cells that have scent, e.g. ["3,3", "0,0"]
     */
    public function draw(int $maxX, int $maxY, array $roverPositions, array $scentCells): string
    {
        $roverSet = [];
        foreach ($roverPositions as [$x, $y]) {
            $roverSet["{$x},{$y}"] = true;
        }
        $scentSet = \array_flip($scentCells);

        $lines = [];
        $colCount = $maxX + 1;

        // Top border: ┌──┬──┬──...┬──┐
        $lines[] = self::BOX['top_left'] . \str_repeat(self::BOX['line'], self::CELL_WIDTH);
        for ($c = 1; $c < $colCount; $c++) {
            $lines[0] .= self::BOX['top_T'] . \str_repeat(self::BOX['line'], self::CELL_WIDTH);
        }
        $lines[0] .= self::BOX['top_right'];

        // Rows: y from maxY down to 0 (North up)
        for ($y = $maxY; $y >= 0; $y--) {
            $row = self::BOX['wall'];
            for ($x = 0; $x <= $maxX; $x++) {
                $key = "{$x},{$y}";
                $hasRover = isset($roverSet[$key]);
                $hasScent = isset($scentSet[$key]);
                $cell = ' ';
                if ($hasRover) $cell .= 'R';
                if ($hasScent) $cell .= 'S';
                $cell = \str_pad($cell, self::CELL_WIDTH, ' ', \STR_PAD_RIGHT);
                $row .= $cell . self::BOX['wall'];
            }
            $lines[] = $row;

            if ($y > 0) {
                $sep = self::BOX['left_T'] . \str_repeat(self::BOX['line'], self::CELL_WIDTH);
                for ($c = 1; $c < $colCount; $c++) {
                    $sep .= self::BOX['cross_bar'] . \str_repeat(self::BOX['line'], self::CELL_WIDTH);
                }
                $sep .= self::BOX['right_T'];
                $lines[] = $sep;
            }
        }

        // Bottom border: └──┴──...┴──┘
        $bottom = self::BOX['bottom_left'] . \str_repeat(self::BOX['line'], self::CELL_WIDTH);
        for ($c = 1; $c < $colCount; $c++) {
            $bottom .= self::BOX['bottom_T'] . \str_repeat(self::BOX['line'], self::CELL_WIDTH);
        }
        $bottom .= self::BOX['bottom_right'];
        $lines[] = $bottom;

        return \implode("\n", $lines);
    }
}
