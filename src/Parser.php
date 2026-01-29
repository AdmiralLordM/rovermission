<?php

declare(strict_types=1);

namespace Rovers;

/**
 * Parses input file content into terrain size and rover initial positions + instructions.
 */
final class Parser
{
    /**
     * Parse input string. Returns [Terrain, list of [x, y, Orientation, instructions]].
     *
     * @return array{0: Terrain, 1: list<array{0: int, 1: int, 2: Orientation, 3: string}>}
     */
    public function parse(string $input): array
    {
        $lines = $this->normalizeLines($input);
        if (\count($lines) < 1) {
            throw new \InvalidArgumentException('Input must contain at least the terrain line');
        }

        $terrain = $this->parseTerrainLine($lines[0]);
        $rovers = [];
        $i = 1;

        while ($i < \count($lines)) {
            $positionLine = $lines[$i];
            $i++;
            if ($i >= \count($lines)) {
                throw new \InvalidArgumentException('Missing instruction line for rover');
            }
            $instructionLine = $lines[$i];
            $i++;

            [$x, $y, $orientation] = $this->parsePositionLine($positionLine);
            $instructions = \trim($instructionLine);
            $rovers[] = [$x, $y, $orientation, $instructions];
        }

        return [$terrain, $rovers];
    }

    /**
     * @return list<string>
     */
    private function normalizeLines(string $input): array
    {
        $lines = \explode("\n", $input);
        return \array_values(\array_filter(\array_map('trim', $lines), static fn(string $l) => $l !== ''));
    }

    private function parseTerrainLine(string $line): Terrain
    {
        $parts = \preg_split('/\s+/', $line, -1, \PREG_SPLIT_NO_EMPTY);
        if (\count($parts) !== 2) {
            throw new \InvalidArgumentException("Terrain line must be 'maxX maxY': {$line}");
        }
        $maxX = (int) $parts[0];
        $maxY = (int) $parts[1];
        if ($maxX > 50 || $maxY > 50) {
            throw new \InvalidArgumentException('Maximum value for any coordinate is 50');
        }
        return new Terrain($maxX, $maxY);
    }

    /**
     * @return array{0: int, 1: int, 2: Orientation}
     */
    private function parsePositionLine(string $line): array
    {
        $parts = \preg_split('/\s+/', $line, -1, \PREG_SPLIT_NO_EMPTY);
        if (\count($parts) !== 3) {
            throw new \InvalidArgumentException("Position line must be 'x y O': {$line}");
        }
        $x = (int) $parts[0];
        $y = (int) $parts[1];
        $orientation = Orientation::fromString($parts[2]);
        return [$x, $y, $orientation];
    }
}
