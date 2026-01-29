<?php

declare(strict_types=1);

namespace Rovers\Tests\Command;

use PHPUnit\Framework\TestCase;
use Rovers\Command\CommandFactory;
use Rovers\Command\ForwardCommand;
use Rovers\Orientation;
use Rovers\Rover;
use Rovers\Terrain;

final class CommandsTest extends TestCase
{
    public function testForwardCommandMovesRover(): void
    {
        $terrain = new Terrain(5, 5);
        $rover = new Rover(2, 2, Orientation::north(), $terrain);
        $cmd = new ForwardCommand();
        $cmd->execute($rover);
        $this->assertSame(2, $rover->x());
        $this->assertSame(3, $rover->y());
    }

    public function testCommandFactoryCreatesCommands(): void
    {
        $this->assertSame('L', CommandFactory::create('L')->getLetter());
        $this->assertSame('R', CommandFactory::create('R')->getLetter());
        $this->assertSame('F', CommandFactory::create('F')->getLetter());
    }

    public function testCommandFactoryForUnknownCommand(): void
    {
        $this->assertSame('-', CommandFactory::create('X', true)->getLetter());
    }

    
}
