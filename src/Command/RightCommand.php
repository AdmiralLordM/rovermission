<?php

declare(strict_types=1);

namespace Rovers\Command;

use Rovers\Rover;

final class RightCommand implements CommandInterface
{
    public function getLetter(): string
    {
        return 'R';
    }

    public function execute(Rover $rover): bool
    {
        $rover->turnRight();
        return true;
    }
}
