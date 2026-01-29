<?php

declare(strict_types=1);

namespace Rovers;

use Rovers\Command\CommandFactory;

/**
 * A rover on the terrain with position, orientation, and command execution.
 */
final class Rover
{
    private bool $lost = false;

    public function __construct(
        private int $x,
        private int $y,
        private Orientation $orientation,
        private readonly Terrain $terrain
    ) {
        if (!$terrain->isInBounds($x, $y)) {
            throw new \InvalidArgumentException("Initial position ({$x}, {$y}) is out of bounds");
        }
    }

    public function x(): int
    {
        return $this->x;
    }

    public function y(): int
    {
        return $this->y;
    }

    public function orientation(): Orientation
    {
        return $this->orientation;
    }

    public function isLost(): bool
    {
        return $this->lost;
    }


    public function turnLeft(): void
    {
        $this->orientation = $this->orientation->turnLeft();
    }

    public function turnRight(): void
    {
        $this->orientation = $this->orientation->turnRight();
    }

    /**
     * Move forward one step. 
     * Check 'scent' first : if it extis in this place (with same structure (orientation and command):ignore;
     * else exectue the move
     * if we end up falling off, mark the grid with our scent  (so the next guy wont fall off here...)
     *
     * @param string $commandLetter Letter used for scent (e.g. 'F' for Forward)
     */
    public function moveForward(string $commandLetter = 'F'): bool
    {
        if ($this->lost) {
            return false;
        }

        [$dx, $dy] = $this->orientation->forwardDelta();
        $nextX = $this->x + $dx;
        $nextY = $this->y + $dy;

        if ($this->terrain->isInBounds($nextX, $nextY)) {
            //yay! we are safe! move on...
            //var_dump("safe move to {$nextX}, {$nextY}");
            $this->x = $nextX;
            $this->y = $nextY;
            return true;
        }


        if ($this->terrain->hasScent($this->x, $this->y, $this->orientation->value(), $commandLetter)) {
            //skip movement and pretend that we havent even seen it...
            //var_dump("scent found, skipping move");
            return true;
        }

        //rover fallen off Mars... :( last ditch effort to prevent the next rover from dying
        //remember Opportunity "My batteries are low and it's getting dark" 
        $this->terrain->addScent($this->x, $this->y, $this->orientation->value(), $commandLetter);
        $this->lost = true;
        //rover is lost, so we return false
        //var_dump("rover is lost, adding scent and returning false", $this->x, $this->y, $this->orientation->value(), $commandLetter);
        return false;
    }

    /**
     * Execute a single instruction letter (L, R, F ...). Returns false if rover is lost after this command.
     */
    public function executeInstruction(string $letter): bool
    {
        if ($this->lost) {
            return false;
        }
        $command = CommandFactory::create($letter);
        try {
            $result = $command->execute($this);
        } catch (\LogicException $e) {
            echo "\033[31mRover Malfunction: {$e->getMessage()}\033[0m\n";
            return false;
        } catch (\Throwable $e) {
            echo "\033[31mRover Malfunction: {$e->getMessage()}\033[0m\n";
            return false;
        }
        return $result;
    }

    /**
     * Execute a string of instructions. Stops when rover is lost.
     */
    public function executeInstructions(string $instructions): void
    {
        $letters = \preg_split('//u', \trim($instructions), -1, \PREG_SPLIT_NO_EMPTY) ?: [];
        //var_dump($letters);
        foreach ($letters as $letter) {
            if ($letter === '') {
                continue;
            }
            if (!$this->executeInstruction($letter)) {
                break;
            }
        }
    }

    /**
     * Final position line for output: "x y O" or "x y O LOST".
     */
    public function getOutputLine(): string
    {
        $line = "{$this->x} {$this->y} {$this->orientation->value()}";
        if ($this->lost) {
            $line .= ' LOST';
        }
        return $line;
    }
}
