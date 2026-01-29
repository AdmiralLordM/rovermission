#!/usr/bin/env php
<?php

declare(strict_types=1);

/**
 * Generates test input to stdout.
 * Usage: php bin/generateData.php [INTxINT]
 * Example: php bin/generateData.php 20x20
 * Example: php bin/generateData.php 50x50 > test.dat
 *
 * INTxINT = max terrain X and Y (e.g. 20x20 → grid 0..20, 0..20). Default 50x50.
 * Number of robots = 10% of grid cells ( (maxX+1)*(maxY+1) ), minimum 1.
 * Commands are random with bias toward F; in-place rotation limited to 4 L/R then move.
 */

const DEFAULT_TERRAIN = '50x50';
const COMMANDS_PER_ROBOT = 99; // spec: instruction string < 100 chars
const ORIENTATIONS = ['N', 'S', 'E', 'W'];
const MAX_CONSECUTIVE_ROTATIONS = 4; // one full turn, then move
const ROBOT_PERCENT = 0.1; // 10% of grid cells

/**
 * Parse INTxINT from argument. Returns [maxX, maxY] or null if invalid.
 *
 * @return array{0: int, 1: int}|null
 */
function parseTerrainArg(string $arg): ?array {
    if (!\preg_match('/^(\d+)x(\d+)$/i', \trim($arg), $m)) {
        return null;
    }
    $maxX = (int) $m[1];
    $maxY = (int) $m[2];
    if ($maxX > 50 || $maxY > 50 || $maxX < 0 || $maxY < 0) {
        return null;
    }
    return [$maxX, $maxY];
}

function generateInstructionString(): string {
    $chars = [];
    $consecutiveRotations = 0;
    for ($i = 0; $i < COMMANDS_PER_ROBOT; $i++) {
        if ($consecutiveRotations >= MAX_CONSECUTIVE_ROTATIONS) {
            $chars[] = 'F';
            $consecutiveRotations = 0;
            continue;
        }
        $r = random_int(1, 100);
        if ($r <= 55) {
            $chars[] = 'F';
            $consecutiveRotations = 0;
        } elseif ($r <= 75) {
            $chars[] = 'L';
            $consecutiveRotations++;
        } else if ($r <= 98) {
            $chars[] = 'R';
            $consecutiveRotations++;
        } else {
            //some action either photo or sample
            $chars[] = random_int(1, 2) === 1 ? 'P' : 'S';
        }
    }
    
    $line = \implode('', $chars);
    //replace FF with H to simulate hop command
    $line = \str_replace('FF', 'H', $line);
    return $line;
}

function main(): void {
    $args = $GLOBALS['argv'] ?? [];
    $arg = isset($args[1]) ? $args[1] : DEFAULT_TERRAIN;
    $parsed = parseTerrainArg($arg);
    if ($parsed === null) {
        \fwrite(\STDERR, "Usage: php bin/generateData.php [INTxINT]\n");
        \fwrite(\STDERR, "Example: php bin/generateData.php 20x20\n");
        \fwrite(\STDERR, "INTxINT must be 0..50 (e.g. 20x20). Omit for default 50x50.\n");
        exit(1);
    }
    [$maxX, $maxY] = $parsed;

    $gridCells = ($maxX+1) * ($maxY+1);
    $numRobots = (int) \max(1, \floor($gridCells * ROBOT_PERCENT));

    $lines = [];
    $lines[] = $maxX . ' ' . $maxY;

    for ($r = 0; $r < $numRobots; $r++) {
        $x = random_int(0, $maxX);
        $y = random_int(0, $maxY);
        $o = ORIENTATIONS[array_rand(ORIENTATIONS)];
        $lines[] = "{$x} {$y} {$o}";
        $lines[] = generateInstructionString();
    }

    echo \implode("\n", $lines) . "\n";
}

main();
