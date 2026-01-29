<?php

declare(strict_types=1);

namespace Rovers;

/**
 * Rectangular grid (0,0) to (maxX, maxY). Tracks scents: (orientation, command) that caused loss at a cell.
 */
final class Terrain
{
    /** @var array<string, true> key = "x,y,orientation,command" */
    private array $scents = [];

    public function __construct(
        private readonly int $maxX,
        private readonly int $maxY
    ) {
        if ($maxX < 0 || $maxY < 0) {
            throw new \InvalidArgumentException('Terrain bounds must be non-negative');
        }
    }

    public function maxX(): int
    {
        return $this->maxX;
    }

    public function maxY(): int
    {
        return $this->maxY;
    }

    public function isInBounds(int $x, int $y): bool
    {
        return $x >= 0 && $x <= $this->maxX && $y >= 0 && $y <= $this->maxY;
    }

    /**
     * Check if moving with this orientation and command from this cell would be ignored (scent).
     */
    public function hasScent(int $x, int $y, string $orientation, string $command): bool
    {
        $key = $this->scentKey($x, $y, $orientation, $command);
        return isset($this->scents[$key]);
    }

    /**
     * Record that a rover was lost at this cell when executing this orientation and command.
     */
    public function addScent(int $x, int $y, string $orientation, string $command): void
    {
        $key = $this->scentKey($x, $y, $orientation, $command);
        $this->scents[$key] = true;
    }

    private function scentKey(int $x, int $y, string $orientation, string $command): string
    {
        return "{$x},{$y},{$orientation},{$command}";
    }

    /**
     * Return all scent keys for debug (e.g. ["x,y,O,cmd", ...]).
     *
     * @return list<string>
     */
    public function getScents(): array
    {
        return array_keys($this->scents);
    }
}
