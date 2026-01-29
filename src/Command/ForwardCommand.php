<?php

declare(strict_types=1);

namespace Rovers\Command;

use Rovers\Rover;

final class ForwardCommand implements CommandInterface
{
    public function getLetter(): string
    {
        return 'F';
    }

    public function execute(Rover $rover): bool
    {
        return $rover->moveForward();
    }
}
