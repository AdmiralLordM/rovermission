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
        ];

        
    }

    public static function create(string $letter): CommandInterface
    {
        self::init();
        $letter = \strtoupper(\trim($letter));
        if (!isset(self::$commands[$letter])) {
            throw new \InvalidArgumentException("Unknown command: {$letter}");
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
