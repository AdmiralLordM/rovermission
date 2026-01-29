<?php

declare(strict_types=1);

namespace Rovers;

/**
 * Represents rover orientation (N, S, E, W) and provides turn/move deltas.
 */
final class Orientation
{
    private const VALID = ['N', 'S', 'E', 'W'];

    public function __construct(
        private readonly string $value
    ) {
        if (!\in_array($value, self::VALID, true)) {
            throw new \InvalidArgumentException("Invalid orientation: {$value}");
        }
    }

    public static function north(): self
    {
        return new self('N');
    }

    public static function south(): self
    {
        return new self('S');
    }

    public static function east(): self
    {
        return new self('E');
    }

    public static function west(): self
    {
        return new self('W');
    }

    public static function fromString(string $value): self
    {
        return new self(\strtoupper(\trim($value)));
    }

    public function value(): string
    {
        return $this->value;
    }

    public function turnLeft(): self
    {
        return match ($this->value) {
            'N' => self::west(),
            'W' => self::south(),
            'S' => self::east(),
            'E' => self::north(),
            default => throw new \LogicException("Unknown orientation: {$this->value}"),
        };
    }

    public function turnRight(): self
    {
        return match ($this->value) {
            'N' => self::east(),
            'E' => self::south(),
            'S' => self::west(),
            'W' => self::north(),
            default => throw new \LogicException("Unknown orientation: {$this->value}"),
        };
    }

    /**
     * Returns [dx, dy] for one step forward in this orientation.
     * North = (x, y) -> (x, y+1).
     */
    public function forwardDelta(): array
    {
        return match ($this->value) {
            'N' => [0, 1],
            'S' => [0, -1],
            'E' => [1, 0],
            'W' => [-1, 0],
            default => throw new \LogicException("Unknown orientation: {$this->value}"),
        };
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }
}
