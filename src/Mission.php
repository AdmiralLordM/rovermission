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
     * @return list<string>|array{output: list<string>, debug?: array, visualise?: array{maxX: int, maxY: int, roverPositions: list<array{0: int, 1: int}>, scentCells: list<string>}}
     */
    public function run(string $input): array
    {
        //well... not running yet...just trying to parse the data...
        [$terrain, $roversData] = $this->parser->parse($input);
        $output = [];

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
        }

        return $output;
       
    }

   
}
