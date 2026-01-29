<?php

declare(strict_types=1);

namespace Rovers\Command;

use Rovers\Rover;

/**
 * Single instruction that can be executed on a rover. Extend for command types (L, R, F, ...).
 */
interface CommandInterface
{
    public function getLetter(): string;

    /**
     * Execute this command on the rover. Returns true if the rover is still active (not lost).
     */
    public function execute(Rover $rover): bool;
}
