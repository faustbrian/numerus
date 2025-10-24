<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\Numerus;

use InvalidArgumentException;

use function abs;
use function throw_if;

/**
 * Static utility class for percentage calculations.
 *
 * Provides convenient methods for common percentage operations including
 * calculating percentages, percentage differences, and applying percentage
 * adjustments. All methods are static and accept both primitive numeric
 * types and Numerus instances for flexible usage.
 *
 * ```php
 * Percentage::of(25, 100);              // 25.0
 * Percentage::differenceBetween(50, 75); // 50.0
 * Percentage::add(20, 100);             // 120.0
 * ```
 *
 * @author Brian Faust <brian@cline.sh>
 */
final class Percentage
{
    /**
     * Calculate what percentage one value is of another.
     *
     * Determines the percentage that a part represents of a total value,
     * commonly used for completion ratios, progress indicators, and
     * statistical analysis.
     *
     * ```php
     * Percentage::of(25, 100); // 25.0 (25 is 25% of 100)
     * Percentage::of(50, 200); // 25.0 (50 is 25% of 200)
     * ```
     *
     * @param float|int|Numerus $part  The partial value to calculate percentage for
     * @param float|int|Numerus $total The total value to calculate against
     *
     * @throws InvalidArgumentException When total is zero
     *
     * @return float The percentage the part represents of the total
     */
    public static function of(int|float|Numerus $part, int|float|Numerus $total): float
    {
        $partValue = $part instanceof Numerus ? $part->value() : $part;
        $totalValue = $total instanceof Numerus ? $total->value() : $total;

        throw_if($totalValue === 0 || $totalValue === 0.0, InvalidArgumentException::class, 'Cannot calculate percentage with zero total');

        return ($partValue / $totalValue) * 100;
    }

    /**
     * Calculate the percentage difference between two values.
     *
     * Computes the relative change from an original value to a new value,
     * expressed as a percentage. Returns positive values for increases and
     * negative values for decreases. Useful for trend analysis, growth
     * calculations, and year-over-year comparisons.
     *
     * ```php
     * Percentage::differenceBetween(50, 75);  // 50.0 (50% increase)
     * Percentage::differenceBetween(100, 80); // -20.0 (20% decrease)
     * ```
     *
     * @param float|int|Numerus $original The starting or baseline value
     * @param float|int|Numerus $new      The ending or comparison value
     *
     * @throws InvalidArgumentException When original value is zero
     *
     * @return float The percentage change (positive for increase, negative for decrease)
     */
    public static function differenceBetween(int|float|Numerus $original, int|float|Numerus $new): float
    {
        $originalValue = $original instanceof Numerus ? $original->value() : $original;
        $newValue = $new instanceof Numerus ? $new->value() : $new;

        throw_if($originalValue === 0 || $originalValue === 0.0, InvalidArgumentException::class, 'Cannot calculate percentage difference from zero');

        return (($newValue - $originalValue) / $originalValue) * 100;
    }

    /**
     * Calculate the absolute percentage difference between two values.
     *
     * Returns the magnitude of the percentage change without regard to
     * direction (increase vs decrease). Useful when only the size of the
     * change matters, not the direction.
     *
     * ```php
     * Percentage::absoluteDifferenceBetween(100, 80);  // 20.0
     * Percentage::absoluteDifferenceBetween(80, 100);  // 25.0
     * Percentage::absoluteDifferenceBetween(50, 75);   // 50.0
     * ```
     *
     * @param float|int|Numerus $a The first value for comparison
     * @param float|int|Numerus $b The second value for comparison
     *
     * @throws InvalidArgumentException When the first value is zero
     *
     * @return float The absolute percentage difference (always positive)
     */
    public static function absoluteDifferenceBetween(int|float|Numerus $a, int|float|Numerus $b): float
    {
        return abs(self::differenceBetween($a, $b));
    }

    /**
     * Calculate X percent of a given number.
     *
     * Computes the actual numeric value that represents a percentage of
     * a base number. Commonly used for discount calculations, tax amounts,
     * or proportional distributions.
     *
     * ```php
     * Percentage::calculate(20, 100); // 20.0 (20% of 100)
     * Percentage::calculate(15, 80);  // 12.0 (15% of 80)
     * Percentage::calculate(5, 200);  // 10.0 (5% of 200)
     * ```
     *
     * @param  float|int         $percentage The percentage value (e.g., 20 for 20%)
     * @param  float|int|Numerus $number     The base number to calculate percentage of
     * @return float             The calculated percentage amount
     */
    public static function calculate(int|float $percentage, int|float|Numerus $number): float
    {
        $numberValue = $number instanceof Numerus ? $number->value() : $number;

        return $numberValue * ($percentage / 100);
    }

    /**
     * Add a percentage to a number.
     *
     * Increases a value by a specified percentage of itself, useful for
     * markup calculations, price increases, or compound adjustments.
     *
     * ```php
     * Percentage::add(20, 100); // 120.0 (add 20% to 100)
     * Percentage::add(10, 50);  // 55.0 (add 10% to 50)
     * Percentage::add(5, 200);  // 210.0 (add 5% to 200)
     * ```
     *
     * @param  float|int         $percentage The percentage to add (e.g., 20 for 20%)
     * @param  float|int|Numerus $number     The base number to increase
     * @return float             The number with the percentage added
     */
    public static function add(int|float $percentage, int|float|Numerus $number): float
    {
        $numberValue = $number instanceof Numerus ? $number->value() : $number;

        return $numberValue + self::calculate($percentage, $numberValue);
    }

    /**
     * Subtract a percentage from a number.
     *
     * Decreases a value by a specified percentage of itself, commonly used
     * for discount calculations, price reductions, or depreciation.
     *
     * ```php
     * Percentage::subtract(20, 100); // 80.0 (subtract 20% from 100)
     * Percentage::subtract(10, 50);  // 45.0 (subtract 10% from 50)
     * Percentage::subtract(5, 200);  // 190.0 (subtract 5% from 200)
     * ```
     *
     * @param  float|int         $percentage The percentage to subtract (e.g., 20 for 20%)
     * @param  float|int|Numerus $number     The base number to decrease
     * @return float             The number with the percentage subtracted
     */
    public static function subtract(int|float $percentage, int|float|Numerus $number): float
    {
        $numberValue = $number instanceof Numerus ? $number->value() : $number;

        return $numberValue - self::calculate($percentage, $numberValue);
    }
}
