#!/usr/bin/env php
<?php

declare(strict_types=1);

/**
 * Generates data.dat: large terrain (50x50), 100 rovers, 99 commands each.
 * Commands are random with bias toward F (movement); in-place rotation limited to
 * at most 4 consecutive L/R (one "panoramic" turn) then we force a move.
 */

const MAX_TERRAIN_X = 50;
const MAX_TERRAIN_Y = 50;
const NUM_ROBOTS = 100;
const COMMANDS_PER_ROBOT = 99; // spec: instruction string < 100 chars
const ORIENTATIONS = ['N', 'S', 'E', 'W'];
const MAX_CONSECUTIVE_ROTATIONS = 4; // one full turn, then move

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
    $lines = [];
    $lines[] = MAX_TERRAIN_X . ' ' . MAX_TERRAIN_Y;

    for ($r = 0; $r < NUM_ROBOTS; $r++) {
        $x = random_int(0, MAX_TERRAIN_X);
        $y = random_int(0, MAX_TERRAIN_Y);
        $o = ORIENTATIONS[array_rand(ORIENTATIONS)];
        $lines[] = "{$x} {$y} {$o}";
        $lines[] = generateInstructionString();
    }

    echo implode("\n", $lines) . "\n";
}

main();
