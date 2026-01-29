<?php

declare(strict_types=1);

namespace Rovers\Command;

use Rovers\Rover;

/**
 * H = hop: move two tiles forward (double forward). 
 * Uses scent key 'F' so both types (H and F) prevent the next rover from dying (regardless of the command used)
 */
final class HopCommand implements CommandInterface
{
    public function getLetter(): string
    {
        return 'H';
    }

    public function execute(Rover $rover): bool
    {
        $stillAlive = $rover->moveForward('F');
        return $stillAlive ? $rover->moveForward('F') : false;
    }
}
