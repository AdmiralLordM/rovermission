<?php

declare(strict_types=1);

namespace Rovers\Command;

use Rovers\Rover;

final class LeftCommand implements CommandInterface
{
    public function getLetter(): string
    {
        return 'L';
    }

    public function execute(Rover $rover): bool
    {
        $rover->turnLeft();
        return true;
    }
}
