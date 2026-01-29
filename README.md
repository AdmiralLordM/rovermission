# Mars Rovers

A command-line Mars Rover Mission (simulation) of rovers moving on a rectangular grid (Mars). Rovers follow instructions (L, R, F) and report their final position; those that move off the grid are lost, and their “scent” prevents later rovers from making the same fatal move from the same cell in the same orientation.

No need to worry however the communication delay is not implemented - so you don't have to wait half an hour to see the results :)

## Requirements

- **PHP** 8.1 or higher
- **Composer** (for dependencies and autoloading)

## Installation

```bash
composer install
```

This installs the application code (autoload only) and dev dependencies (PHPUnit for testing).

## How to Run

Run the Mission (simulation) by passing an **input file path** as the first argument. Output is printed to **stdout** (the screen).

```bash
php bin/run.php <filename>
```

**Example** (using the included sample file):

```bash
php bin/run.php test.dat
```

You can also create your own file (e.g. `myinput.dat`) with the format described below and run:

```bash
php bin/run.php myinput.dat
```

**Debug mode:** Add the `-debug` flag (before or after the filename) to print execution summary after the standard output: terrain bounds, total rovers, number of lost rovers, and all scents (grouped by cell). Useful to assess run data.

```bash
php bin/run.php -debug test.dat
php bin/run.php test.dat -debug
```

**Visualise mode:** Add the `-visualise` flag to draw the grid on the terminal after the standard output (and after debug, if present). Uses ASCII box-drawing characters (┌ ┬ ┐ ├ ┼ ┤ └ ┴ ┘ │). Each cell is 2 characters wide and 1 tall. `R` = surviving rover, `S` = scent (where a rover was lost), `RS` = both; lost rovers are not shown. Row at top is North (y = maxY). Note: on terminals with variable character width the grid may look distorted.

```bash
php bin/run.php -visualise test.dat
php bin/run.php -debug -visualise test.dat
```

## Generate Additional test data

To create a large random input file for testing, use the generator script. It writes to **stdout**; redirect to a file to save it.

```bash
php bin/generateData.php > data.dat
```

The generated file has:

- **Terrain:** 50×50 (upper-right coordinates).
- **Rovers:** 100 rovers, each with a random starting position and orientation.
- **Commands per rover:** 99 (max allowed by the spec). Commands are random with a bias toward `F` (movement); in-place rotation is limited to at most 4 consecutive L/R (one “panoramic” turn), then a move is forced.

Then run the Mission you generated:

```bash
php bin/run.php datatest.dat
php bin/run.php -debug datatest.dat
```

## How to Test

The project uses **PHPUnit** for unit tests. Run all tests with:

```bash
./vendor/bin/phpunit
```

Or, if you have PHPUnit on your PATH:

```bash
phpunit
```

Tests live in the `tests/` directory and mirror the structure of `src/`. To add a new test, create or extend a `*Test.php` file in `tests/` (or `tests/Command/`, etc.) and run the suite again.

## Project Structure

| Path | Purpose |
| ------ | -------- |
| `bin/run.php` | CLI entry point: reads a file, runs the Mission (simulation), prints results to stdout. |
| `bin/generateData.php` | Generates random test input (50×50, 100 rovers, 99 commands each) to stdout. |
| `src/` | Application code (namespaced under `Rovers\`). |
| `src/GridVisualiser.php` | Draws the terrain grid with box-drawing chars when `-visualise` is used. |
| `tests/` | PHPUnit tests (namespaced under `Rovers\Tests\`). |
| `phpunit.xml` | PHPUnit configuration and test discovery. |

### Main Classes and Responsibilities

- **`Orientation`** — N/S/E/W; turning left/right; forward step deltas (North = (x, y) → (x, y+1)).
- **`Terrain`** — Grid bounds (0,0 to maxX, maxY), in-bounds check, scent storage. Scent is per **(x, y, orientation, command)** so multiple “death” directions on one tile are supported.
- **`Command\CommandInterface`** — Contract for a single instruction (L, R, F, or future commands).
- **`Command\LeftCommand`**, **`RightCommand`**, **`ForwardCommand`**, **`PhotoCommand`**, **`SampleCommand`**, **`HopCommand`** — L, R, F, P, S, H.
- **`Command\CommandFactory`** — Maps a letter to a command instance; register new command types here to extend behaviour.
- **`Rover`** — Position, orientation, memory (photos/samples), execution of instructions; forward/hop moves use terrain for bounds and scent.
- **`Parser`** — Parses input text into terrain size and a list of (position + instruction string) per rover.
- **`Mission`** — Runs the full flow: parse input, run each rover in order, return output lines.

## Extending the Project

- **New command types** — Implement `Rovers\Command\CommandInterface`, then register the command in `CommandFactory::register()` (or in the factory’s internal map). The parser does not need to change as long as instructions are still single letters.
- **New tests** — Add classes in `tests/` (e.g. `tests/SomeNewClassTest.php`) that extend `PHPUnit\Framework\TestCase` and use the `Rovers\` and `Rovers\Tests\` namespaces as in existing tests.

## Input Format

- **Line 1:** Upper-right grid coordinates (two integers, space-separated). Lower-left is always (0, 0).
- **Then, for each rover, two lines:**
  1. **Position:** `x y O` — two integers and an orientation (`N`, `S`, `E`, `W`), space-separated.
  2. **Instructions:** A single line of letters. Core: `L` (left 90°), `R` (right 90°), `F` (forward one step). Extra: `P` (photo — store current coordinates in rover memory), `S` (sample — store current coordinates as terrain sample in memory), `H` (hop — move two tiles forward; uses its own scent key so a lost hop is remembered separately from `F`).

Constraints (from the problem): max coordinate 50; instruction string &lt; 100 characters. Each rover has a memory (array) holding `photos` and `samples` (lists of `[x, y]` coordinates) populated by `P` and `S`.

## Output Format

One line per rover: `x y O` or `x y O LOST` if the rover moved off the grid. Orientation is the final direction before the program ended (or before it was lost).

## Sample Input (copy-paste into a file)

You can save this as e.g. `sample.dat` and run `php bin/run.php sample.dat`:

```text
5 3
1 1 E
RFRFRFRF
3 2 N
FRRFLLFFRRFLL
0 3 W
LLFFFLFLFL
```

Meaning:

- Grid from (0, 0) to (5, 3).
- Rover 1: start (1, 1) East, instructions `RFRFRFRF`.
- Rover 2: start (3, 2) North, instructions `FRRFLLFFRRFLL`.
- Rover 3: start (0, 3) West, instructions `LLFFFLFLFL`.

## Sample Output (expected result for the input above)

```text
1 1 E
3 3 N LOST
2 3 S
```

- Rover 1 ends at (1, 1) East (stays in bounds).
- Rover 2 ends at (3, 3) North and is LOST (fell off the top); its scent at (3, 3) facing North with “F” causes the next rover’s same move to be ignored.
- Rover 3 ends at (2, 3) South (the fatal F from (3, 3) North is ignored thanks to the scent, so it does not fall off).

## Scent Behaviour

When a rover moves off the grid with “F”, a **scent** is stored for that **(x, y, orientation, command)** — in practice, **(x, y, orientation, F)**. Any later rover on the same cell that would execute the same move (same orientation, same “F”) has that move **ignored** (it does not move and does not get lost). The coordinate is **not** blocked: other commands (L, R) or “F” in other directions are still allowed. Multiple scents can exist on one tile (e.g. one per direction on a corner or on a 1×1 grid).

## License

Use and modify as needed for your environment.
