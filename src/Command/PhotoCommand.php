<?php

declare(strict_types=1);

namespace Rovers\Command;

use Rovers\Rover;

/**
 * P = take a photo: store current coordinates and orientation in rover memory under "photos".
 */
final class PhotoCommand implements CommandInterface
{
    public function getLetter(): string
    {
        return 'P';
    }

    public function execute(Rover $rover): bool
    {
        $rover->recordPhoto();
        return true;
    }
}
