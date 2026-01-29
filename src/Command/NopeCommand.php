<?php

declare(strict_types=1);

namespace Rovers\Command;

use Rovers\Rover;

final class NopeCommand implements CommandInterface
{
    public function getLetter(): string
    {
        return '-';
    }

    public function execute(Rover $rover): bool
    {
        //do nothing
        return true;
    }
}