<?php

declare(strict_types=1);

namespace Rovers\Command;

use Rovers\Rover;

/**
 * S = sample the terrain: store current coordinates in rover memory under "samples".
 */
final class SampleCommand implements CommandInterface
{
    public function getLetter(): string
    {
        return 'S';
    }

    public function execute(Rover $rover): bool
    {
        $rover->recordSample();
        return true;
    }
}
