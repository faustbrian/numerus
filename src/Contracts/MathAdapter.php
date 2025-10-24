<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\Numerus\Contracts;

use RoundingMode;

/**
 * Contract for mathematical operation adapters.
 *
 * Defines the interface for pluggable math backends that provide arbitrary-precision
 * arithmetic and standard mathematical operations. Implementations can utilize different
 * PHP extensions (BCMath, GMP) or native PHP operators to perform calculations with
 * varying levels of precision and performance characteristics.
 *
 * The adapter pattern allows applications to switch between math engines transparently
 * based on available PHP extensions, required precision, or performance needs. All
 * operations accept flexible input types (int, float, string) and return values in
 * formats appropriate to the underlying implementation.
 *
 * @author Brian Faust <brian@cline.sh>
 *
 * @see \Cline\Numerus\Adapters\BCMathAdapter  High-precision adapter using BCMath extension
 * @see \Cline\Numerus\Adapters\GMPAdapter     Integer-based adapter using GMP extension
 * @see \Cline\Numerus\Adapters\NativeMathAdapter Native PHP math operations
 */
interface MathAdapter
{
    /**
     * Add two numbers together.
     *
     * @param  float|int|string $a The first addend
     * @param  float|int|string $b The second addend
     * @return float|int|string The sum of the two numbers
     */
    public function add(int|float|string $a, int|float|string $b): int|float|string;

    /**
     * Subtract one number from another.
     *
     * @param  float|int|string $a The minuend (number to subtract from)
     * @param  float|int|string $b The subtrahend (number to subtract)
     * @return float|int|string The difference between the two numbers
     */
    public function subtract(int|float|string $a, int|float|string $b): int|float|string;

    /**
     * Multiply two numbers together.
     *
     * @param  float|int|string $a The first factor
     * @param  float|int|string $b The second factor
     * @return float|int|string The product of the two numbers
     */
    public function multiply(int|float|string $a, int|float|string $b): int|float|string;

    /**
     * Divide one number by another.
     *
     * @param  float|int|string $a The dividend (number to be divided)
     * @param  float|int|string $b The divisor (number to divide by)
     * @return float|int|string The quotient of the division
     */
    public function divide(int|float|string $a, int|float|string $b): int|float|string;

    /**
     * Calculate the modulo (remainder) of division.
     *
     * @param  float|int|string $a The dividend
     * @param  float|int|string $b The divisor
     * @return float|int|string The remainder after division
     */
    public function mod(int|float|string $a, int|float|string $b): int|float|string;

    /**
     * Calculate the absolute value of a number.
     *
     * @param  float|int|string $value The number to get the absolute value of
     * @return float|int|string The absolute value (always positive)
     */
    public function abs(int|float|string $value): int|float|string;

    /**
     * Round a number up to the nearest integer.
     *
     * @param  float|int|string $value The number to round up
     * @return float|int|string The ceiling value
     */
    public function ceil(int|float|string $value): int|float|string;

    /**
     * Round a number down to the nearest integer.
     *
     * @param  float|int|string $value The number to round down
     * @return float|int|string The floor value
     */
    public function floor(int|float|string $value): int|float|string;

    /**
     * Round a number to a specified precision using a rounding mode.
     *
     * @param  float|int|string  $value     The number to round
     * @param  int               $precision Number of decimal places to round to
     * @param  null|RoundingMode $mode      The rounding strategy to use. When null, implementations
     *                                      should default to HalfAwayFromZero (standard rounding)
     * @return float|string      The rounded value
     */
    public function round(int|float|string $value, int $precision, ?RoundingMode $mode): float|string;

    /**
     * Raise a number to a power.
     *
     * @param  float|int|string $base     The base number
     * @param  float|int        $exponent The exponent to raise the base to
     * @return float|int|string The result of base raised to exponent
     */
    public function power(int|float|string $base, int|float $exponent): int|float|string;

    /**
     * Calculate the square root of a number.
     *
     * @param  float|int|string $value The number to calculate the square root of
     * @return float|int|string The square root value
     */
    public function sqrt(int|float|string $value): int|float|string;

    /**
     * Compare two numbers.
     *
     * @param  float|int|string $a The first number to compare
     * @param  float|int|string $b The second number to compare
     * @return int              Returns 1 if $a > $b, -1 if $a < $b, or 0 if $a == $b
     */
    public function compare(int|float|string $a, int|float|string $b): int;

    /**
     * Extract the integer part of a number.
     *
     * Truncates toward zero, discarding any fractional component. For negative
     * numbers, this moves toward zero rather than rounding down.
     *
     * @param  float|int|string $value The number to extract the integer part from
     * @return int              The whole number portion
     */
    public function integerPart(int|float|string $value): int;

    /**
     * Extract the fractional part of a number as an absolute value.
     *
     * Returns the decimal portion of the number as a positive value between 0 and 1,
     * regardless of the sign of the original number.
     *
     * @param  float|int|string $value The number to extract the fractional part from
     * @return float|int|string The decimal portion (absolute value)
     */
    public function fractionalPart(int|float|string $value): int|float|string;

    /**
     * Negate a number (multiply by -1).
     *
     * @param  float|int|string $value The number to negate
     * @return float|int|string The negated value
     */
    public function negate(int|float|string $value): int|float|string;

    /**
     * Get the minimum of two numbers.
     *
     * @param  float|int|string $a The first number to compare
     * @param  float|int|string $b The second number to compare
     * @return float|int|string The smaller of the two numbers
     */
    public function min(int|float|string $a, int|float|string $b): int|float|string;

    /**
     * Get the maximum of two numbers.
     *
     * @param  float|int|string $a The first number to compare
     * @param  float|int|string $b The second number to compare
     * @return float|int|string The larger of the two numbers
     */
    public function max(int|float|string $a, int|float|string $b): int|float|string;
}
