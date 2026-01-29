<?php

declare(strict_types=1);

namespace Rovers\Tests;

use PHPUnit\Framework\TestCase;
use Rovers\Orientation;
use Rovers\Rover;
use Rovers\Terrain;

final class RoverTest extends TestCase
{
    public function testTurnLeftAndRight(): void
    {
        $terrain = new Terrain(5, 5);
        $rover = new Rover(0, 0, Orientation::north(), $terrain);
        $rover->turnLeft();
        $this->assertSame('W', $rover->orientation()->value());
        $rover->turnRight();
        $this->assertSame('N', $rover->orientation()->value());
    }

    public function testMoveForwardInBounds(): void
    {
        $terrain = new Terrain(5, 5);
        $rover = new Rover(1, 1, Orientation::east(), $terrain);
        $rover->moveForward();
        $this->assertSame(2, $rover->x());
        $this->assertSame(1, $rover->y());
        $this->assertFalse($rover->isLost());
    }

    public function testMoveForwardOffGridCausesLost(): void
    {
        $terrain = new Terrain(2, 2);
        $rover = new Rover(2, 2, Orientation::north(), $terrain);
        $result = $rover->moveForward();
        $this->assertFalse($result);
        $this->assertTrue($rover->isLost());
        $this->assertSame(2, $rover->x());
        $this->assertSame(2, $rover->y());
        $this->assertTrue($terrain->hasScent(2, 2, 'N', 'F'));
    }

    public function testScentPreventsSecondRoverFalling(): void
    {
        $terrain = new Terrain(2, 2);
        $rover1 = new Rover(2, 2, Orientation::north(), $terrain);
        $rover1->moveForward();
        $this->assertTrue($rover1->isLost());

        $rover2 = new Rover(2, 2, Orientation::north(), $terrain);
        $result = $rover2->moveForward();
        $this->assertTrue($result);
        $this->assertFalse($rover2->isLost());
        $this->assertSame(2, $rover2->x());
        $this->assertSame(2, $rover2->y());
    }

    public function testCoordinateStillUsableForOtherOrientation(): void
    {
        $terrain = new Terrain(2, 2);
        $rover1 = new Rover(2, 2, Orientation::north(), $terrain);
        $rover1->moveForward();
        $this->assertTrue($rover1->isLost());

        $rover2 = new Rover(2, 2, Orientation::east(), $terrain);
        $result = $rover2->moveForward();
        $this->assertFalse($result);
        $this->assertTrue($rover2->isLost());
    }

    public function testExecuteInstructions(): void
    {
        $terrain = new Terrain(5, 5);
        $rover = new Rover(1, 1, Orientation::east(), $terrain);
        $rover->executeInstructions('RFRFRFRF');
        $this->assertSame('1 1 E', $rover->getOutputLine());
    }

    public function testgetOutputLine(): void
    {
        $terrain = new Terrain(1, 1);
        $rover = new Rover(1, 1, Orientation::north(), $terrain);
        $this->assertSame('1 1 N', $rover->getOutputLine());
        $rover->moveForward();
        $this->assertSame('1 1 N LOST', $rover->getOutputLine());
    }

    public function testMemoryStoresPhotosAndSamples(): void
    {
        $terrain = new Terrain(5, 5);
        $rover = new Rover(2, 3, Orientation::east(), $terrain);
        $this->assertSame(['photos' => [], 'samples' => []], $rover->getMemory());
        $rover->recordPhoto();
        $rover->recordSample();
        $rover->moveForward();
        $rover->recordPhoto();
        $mem = $rover->getMemory();
        $this->assertSame([[2, 3, 'E'], [3, 3, 'E']], $mem['photos']);
        $this->assertSame([[2, 3]], $mem['samples']);
    }
}
