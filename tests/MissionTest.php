<?php

declare(strict_types=1);

namespace Rovers\Tests;

use PHPUnit\Framework\TestCase;
use Rovers\Mission;

final class MissionTest extends TestCase
{
    public function testSampleInputMatchesExpectedOutput(): void
    {
        $input = <<<'INPUT'
        5 3
        1 1 E
        RFRFRFRF
        3 2 N
        FRRFLLFFRRFLL
        0 3 W
        LLFFFLFLFL
        INPUT;
        $mission = new Mission();
        $output = $mission->run($input);
        $expected = [
            '1 1 E',
            '3 3 N LOST',
            '2 3 S',
        ];
        $this->assertSame($expected, $output);
    }

    public function testSingleRoverStaysInBounds(): void
    {
        $input = "5 5\n0 0 N\nFFFF";
        $mission = new Mission();
        $output = $mission->run($input);
        $this->assertSame(['0 4 N'], $output);
    }

    public function testRoverFallsOffAndScentSavesSecond(): void
    {
        $input = "1 1\n1 1 N\nF\n1 1 N\nF";
        $mission = new Mission();
        $output = $mission->run($input);
        $this->assertSame('1 1 N LOST', $output[0]);
        $this->assertSame('1 1 N', $output[1]);
    }

    /**
     * One-tile terrain (0,0): 4 rovers die in N, E, S, W. A 5th rover at (0,0) facing N tries F;
     * that move is ignored (scent), so it stays at 0 0 N and is not lost. All four scents on the tile should work.
     */
    public function testMultipleScentsOnOneTileAllIgnored(): void
    {
        $input = <<<'INPUT'
        0 0
        0 0 N
        F
        0 0 E
        F
        0 0 S
        F
        0 0 W
        F
        0 0 N
        F
        INPUT;
        $mission = new Mission();
        $output = $mission->run($input);
        $this->assertSame('0 0 N LOST', $output[0]);
        $this->assertSame('0 0 E LOST', $output[1]);
        $this->assertSame('0 0 S LOST', $output[2]);
        $this->assertSame('0 0 W LOST', $output[3]);
        $this->assertSame('0 0 N', $output[4]); // F ignored due to scent, not lost
    }
}
