<?php

declare(strict_types=1);

namespace Rovers\Tests;

use PHPUnit\Framework\TestCase;
use Rovers\Terrain;

final class TerrainTest extends TestCase
{
    public function testIsInBounds(): void
    {
        $t = new Terrain(5, 3);
        $this->assertTrue($t->isInBounds(0, 0));
        $this->assertTrue($t->isInBounds(5, 3));
        $this->assertTrue($t->isInBounds(2, 2));
        $this->assertFalse($t->isInBounds(6, 3));
        $this->assertFalse($t->isInBounds(3, 4));
        $this->assertFalse($t->isInBounds(-1, 0));
    }

    public function testScent(): void
    {
        $t = new Terrain(5, 3);
        $this->assertFalse($t->hasScent(3, 3, 'N', 'F'));
        $t->addScent(3, 3, 'N', 'F');
        $this->assertTrue($t->hasScent(3, 3, 'N', 'F'));
        $this->assertFalse($t->hasScent(3, 3, 'E', 'F'));
        $this->assertFalse($t->hasScent(2, 3, 'N', 'F'));
    }

    public function testMaxCoordinates(): void
    {
        $t = new Terrain(5, 3);
        $this->assertSame(5, $t->maxX());
        $this->assertSame(3, $t->maxY());
    }

    /**
     * Multiple scents on one tile: on an edge/corner (or 1-tile terrain) you can die in 2–4 directions.
     * Each (orientation, command) is a separate scent; all are stored and all are ignored by later rovers.
     */
    public function testMultipleScentsOnOneTile(): void
    {
        $t = new Terrain(0, 0); // 1 tile only: (0,0)
        $t->addScent(0, 0, 'N', 'F');
        $t->addScent(0, 0, 'E', 'F');
        $t->addScent(0, 0, 'S', 'F');
        $t->addScent(0, 0, 'W', 'F');
        $this->assertTrue($t->hasScent(0, 0, 'N', 'F'));
        $this->assertTrue($t->hasScent(0, 0, 'E', 'F'));
        $this->assertTrue($t->hasScent(0, 0, 'S', 'F'));
        $this->assertTrue($t->hasScent(0, 0, 'W', 'F'));
        $this->assertFalse($t->hasScent(0, 0, 'N', 'L'));
    }
}
