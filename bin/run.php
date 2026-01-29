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

if (\PHP_SAPI !== 'cli') {
    echo "This script must be run from the command line.\n";
    exit(1);
}

$args = $argv ?? [];
$rest = \array_slice($args, 1);

$filenames = \array_values(\array_filter($rest, static fn($a) => \strpos($a, '-') !== 0));
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
    $result = $simulation->run($input);
    foreach ($result as $line) {
        echo $line . "\n";
    }
   
} catch (\Throwable $e) {
    echo "Mission Failed: " . $e->getMessage() . "\n";
    exit(1);
}
