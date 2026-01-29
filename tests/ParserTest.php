<?php

declare(strict_types=1);

namespace Rovers\Tests;

use PHPUnit\Framework\TestCase;
use Rovers\Parser;

final class ParserTest extends TestCase
{
    public function testParseTerrainAndOneRover(): void
    {
        $parser = new Parser();
        $input = "5 3\n1 1 E\nRFRFRFRF";
        [$terrain, $rovers] = $parser->parse($input);
        $this->assertSame(5, $terrain->maxX());
        $this->assertSame(3, $terrain->maxY());
        $this->assertCount(1, $rovers);
        $this->assertSame(1, $rovers[0][0]);
        $this->assertSame(1, $rovers[0][1]);
        $this->assertSame('E', $rovers[0][2]->value());
        $this->assertSame('RFRFRFRF', $rovers[0][3]);
    }

    public function testParseMultipleRovers(): void
    {
        $parser = new Parser();
        $input = "5 3\n1 1 E\nRFRFRFRF\n3 2 N\nFRRFLLFFRRFLL";
        [$terrain, $rovers] = $parser->parse($input);
        $this->assertCount(2, $rovers);
        $this->assertSame(3, $rovers[1][0]);
        $this->assertSame(2, $rovers[1][1]);
        $this->assertSame('N', $rovers[1][2]->value());
        $this->assertSame('FRRFLLFFRRFLL', $rovers[1][3]);
    }

    public function testEmptyInputThrows(): void
    {
        $parser = new Parser();
        $this->expectException(\InvalidArgumentException::class);
        $parser->parse('');
    }
}
