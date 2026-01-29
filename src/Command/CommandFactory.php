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
        self::$commands = [
            'L' => new LeftCommand(),
            'R' => new RightCommand(),
            'F' => new ForwardCommand(),
            '-' => new NopeCommand(),
        ];

        
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
