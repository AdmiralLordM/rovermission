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
        return $this->parser->parse($input);
       
    }

   
}
