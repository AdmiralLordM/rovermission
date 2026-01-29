<?php

declare(strict_types=1);

namespace Rovers\Tests;

use PHPUnit\Framework\TestCase;
use Rovers\Orientation;

final class OrientationTest extends TestCase
{
    public function testFromString(): void
    {
        $n = Orientation::fromString('N');
        $this->assertSame('N', $n->value());
        $this->assertSame('S', Orientation::fromString('s')->value());
    }

    public function testTurnLeft(): void
    {
        $n = Orientation::north();
        $this->assertSame('W', $n->turnLeft()->value());
        $this->assertSame('S', $n->turnLeft()->turnLeft()->value());
        $this->assertSame('E', $n->turnLeft()->turnLeft()->turnLeft()->value());
        $this->assertSame('N', $n->turnLeft()->turnLeft()->turnLeft()->turnLeft()->value());
    }

    public function testTurnRight(): void
    {
        $n = Orientation::north();
        $this->assertSame('E', $n->turnRight()->value());
        $this->assertSame('S', $n->turnRight()->turnRight()->value());
        $this->assertSame('W', $n->turnRight()->turnRight()->turnRight()->value());
        $this->assertSame('N', $n->turnRight()->turnRight()->turnRight()->turnRight()->value());
    }

    public function testForwardDelta(): void
    {
        $this->assertSame([0, 1], Orientation::north()->forwardDelta());
        $this->assertSame([0, -1], Orientation::south()->forwardDelta());
        $this->assertSame([1, 0], Orientation::east()->forwardDelta());
        $this->assertSame([-1, 0], Orientation::west()->forwardDelta());
    }

    public function testInvalidOrientationThrows(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        Orientation::fromString('X');
    }
}
