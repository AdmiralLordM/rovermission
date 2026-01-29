<?php

declare(strict_types=1);

namespace Rovers;

/**
 * Runs the full simulation: parse input, execute each rover, return output lines.
 */
final class Mission
{
    public function __construct(
        private readonly Parser $parser = new Parser()
    ) {
    }

    /**
     * Run simulation on input string.
     *
     * @param bool $withDebug When true, returns array with 'output' and 'debug'.
     * @param bool $withVisualise When true, returns array with 'output' and optionally 'debug' and 'visualise'.
     * @return list<string>|array{output: list<string>, debug?: array, visualise?: array{maxX: int, maxY: int, roverPositions: list<array{0: int, 1: int}>, scentCells: list<string>}}
     */
    public function run(string $input, bool $withDebug = false, bool $withVisualise = false): array
    {
        [$terrain, $roversData] = $this->parser->parse($input);
        $output = [];
        $photosTaken = [];
        $samplesTaken = [];

        foreach ($roversData as [$x, $y, $orientation, $instructions]) {
            if (\strlen($instructions) >= 100) {
                throw new \InvalidArgumentException('Instruction string must be less than 100 characters');
            }
            try {
                $rover = new Rover($x, $y, $orientation, $terrain);
            } catch (\InvalidArgumentException $e) {
                echo "\033[31mRover inoperable: {$e->getMessage()}\033[0m\n";
                continue;
            }
            $rover->executeInstructions($instructions);
            $output[] = $rover->getOutputLine();
            if ($withDebug) {
                $mem = $rover->getMemory();
                foreach ($mem['photos'] as $p) {
                    $photosTaken[] = $p;
                }
                foreach ($mem['samples'] as $s) {
                    $samplesTaken[] = $s;
                }
            }
        }

        $needStructured = $withDebug || $withVisualise;
        if (!$needStructured) {
            return $output;
        }

        $scentKeys = $terrain->getScents();
        $scentsByCell = [];
        $scentCellsUnique = [];
        foreach ($scentKeys as $key) {
            $parts = \explode(',', $key);
            if (\count($parts) === 4) {
                $cell = $parts[0] . ',' . $parts[1];
                $scentsByCell[$cell] = $scentsByCell[$cell] ?? [];
                $scentsByCell[$cell][] = $parts[2] . ' ' . $parts[3];
                $scentCellsUnique[$cell] = true;
            }
        }
        \ksort($scentsByCell);

        $result = [
            'output' => $output,
        ];

        if ($withDebug) {
            $lostRovers = (int) \preg_match_all('/\sLOST$/m', \implode("\n", $output));
            $result['debug'] = [
                'terrain' => '0,0 to ' . $terrain->maxX() . ',' . $terrain->maxY(),
                'totalRovers' => \count($roversData),
                'lostRovers' => $lostRovers,
                'scents' => $scentKeys,
                'scentsByCell' => $scentsByCell,
                'photosTaken' => $photosTaken,
                'samplesTaken' => $samplesTaken,
            ];
        }

        if ($withVisualise) {
            
            $result['visualise'] = [
                'maxX' => $terrain->maxX(),
                'maxY' => $terrain->maxY(),
                'roverPositions' => $this->getRoverPositions($output),
                'scentCells' => \array_keys($scentCellsUnique),
            ];
        }

        return $result;
    }

    /**
     * @param list<string> $output
     * @return list<array{0: int, 1: int}>
     */
    private function getRoverPositions(array $output): array
    {
        $roverPositions = [];
        foreach ($output as $line) {
            if (\str_contains($line, ' LOST')) {
                continue;
            }
            $parts = \preg_split('/\s+/', \trim(\str_replace(' LOST', '', $line)), 3);
            if (\count($parts) >= 2 && \is_numeric($parts[0]) && \is_numeric($parts[1])) {
                $roverPositions[] = [(int) $parts[0], (int) $parts[1]];
            }
        }
        return $roverPositions;
    }
}
