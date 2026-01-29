#!/usr/bin/env php
<?php

declare(strict_types=1);

/**
 * Mars Rovers - CLI entry point.
 * Usage: php bin/run.php <filename>
 * Example: php bin/run.php test.dat
 */

require_once \dirname(__DIR__) . '/vendor/autoload.php';

use Rovers\Mission;
use Rovers\GridVisualiser;

if (\PHP_SAPI !== 'cli') {
    echo "This script must be run from the command line.\n";
    exit(1);
}

$args = $argv ?? [];
$rest = \array_slice($args, 1);
$withDebug = \in_array('-debug', $rest, true);
$withVisualise = \in_array('-visualise', $rest, true);
$filenames = \array_values(\array_filter($rest, static fn($a) => $a !== '-debug' && $a !== '-visualise' && \strpos($a, '-') !== 0));
if (\count($filenames) < 1) {
    echo "Usage: php bin/run.php [ -debug ] [ -visualise ] <filename>\n";
    exit(1);
}

$filename = $filenames[0];
if (!\is_readable($filename)) {
    echo "Error: Cannot read file: {$filename}\n";
    exit(1);
}

$input = \file_get_contents($filename);
if ($input === false) {
    echo "Error: Failed to read file: {$filename}\n";
    exit(1);
}

try {
    $simulation = new Mission();
    $result = $simulation->run($input, $withDebug, $withVisualise);
    
    $lines = \is_array($result) && isset($result['output']) ? $result['output'] : $result;

    foreach ($lines as $line) {
        echo $line . "\n";
    }
   
    if ($withDebug && \is_array($result) && isset($result['debug'])) {
        showDebug($result);
    }
    
    if ($withVisualise && \is_array($result) && isset($result['visualise'])) {
        showVisualise($result);
    }

} catch (\Throwable $e) {
    echo "Mission Failed: " . $e->getMessage() . "\n";
    exit(1);
}


function showDebug(array $result): void
{
    $d = $result['debug'];
    echo "\n--- debug ---\n";
    echo "terrain: " . $d['terrain'] . "\n";
    echo "total rovers: " . $d['totalRovers'] . "\n";
    echo "lost rovers: " . $d['lostRovers'] . "\n";
    echo "scents (count): " . \count($d['scents']) . "\n";
    if (\count($d['scents']) > 0) {
        echo "scents by cell (coordinate => [orientation command]):\n";
        foreach ($d['scentsByCell'] as $cell => $list) {
            echo "  ({$cell}) => [ " . \implode(', ', $list) . " ]\n";
        }
    }
    echo "photos taken: " . \count($d['photosTaken']) . "\n";
    foreach ($d['photosTaken'] as $p) {
        echo "  ({$p[0]}, {$p[1]}) {$p[2]}\n";
    }
    echo "samples taken: " . \count($d['samplesTaken']) . "\n";
    foreach ($d['samplesTaken'] as $s) {
        echo "  ({$s[0]}, {$s[1]})\n";
    }
}

function showVisualise(array $result): void {
    $v = $result['visualise'];
    $visualiser = new GridVisualiser();
    $grid = $visualiser->draw(
        $v['maxX'],
        $v['maxY'],
        $v['roverPositions'],
        $v['scentCells']
    );
    echo "\n--- visualise ---\n";
    echo "R = rover, S = scent, RS = both (lost rovers not shown)\n\n";
    echo $grid . "\n";
}