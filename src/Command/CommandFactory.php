<?php

declare(strict_types=1);

namespace Rovers\Command;

/**
 * Creates command instances by letter. Register new command types here for future extensibility.
 */
final class CommandFactory
{
    /** @var array<string, CommandInterface> */
    private static array $commands = [];
    private static bool $initialized = false;

    private static function init(): void
    {
        if (self::$initialized) return;

        self::$initialized = true;
        self::register(new LeftCommand());
        self::register(new RightCommand());
        self::register(new ForwardCommand());
        
        //testing extra commands - to see if the idea actuall works... (they might even ask this on the interview)
        self::register(new PhotoCommand());
        self::register(new SampleCommand());
        self::register(new HopCommand());
        
        //this is to allow the rovers to continue the mission even if they encounter an unknown command 
        //(who needs a space probe that stops on the first unknown command?
        //(and grinds other rovers to a halt as well :) baad space mission)
        self::register(new NopeCommand()); 

        
    }

    public static function create(string $letter, $silent = false): CommandInterface
    {
        self::init();
        $letter = \strtoupper(\trim($letter));
        if (!isset(self::$commands[$letter])) {
            if (!$silent) echo "\033[31mUnknown Rover Command: {$letter}\033[0m\n";
            //we could throw an exception here, but we don't want to stop the mission for one command that got corrupted(?) in transit between Earth and Mars
            return self::$commands['-'];
        }
        return self::$commands[$letter];
    }

    /**
     * Register a command type.
     */
    public static function register(CommandInterface $command): void
    {
        self::init();
        self::$commands[$command->getLetter()] = $command;
    }
}
