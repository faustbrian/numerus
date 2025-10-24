<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\Numerus\Adapters;

use Cline\Numerus\Contracts\MathAdapter;
use InvalidArgumentException;
use RoundingMode;

use function abs;
use function bcadd;
use function bccomp;
use function bcdiv;
use function bcmod;
use function bcmul;
use function bcpow;
use function bcsqrt;
use function bcsub;
use function explode;
use function extension_loaded;
use function str_contains;
use function throw_if;

/**
 * High-precision mathematical adapter using the BCMath extension.
 *
 * Provides arbitrary-precision arithmetic operations using PHP's BCMath extension,
 * which operates on strings to avoid floating-point precision issues. This adapter
 * is ideal for financial calculations, scientific computing, or any scenario requiring
 * exact decimal arithmetic without rounding errors.
 *
 * All operations preserve precision up to the configured scale (decimal places),
 * making it suitable for currency calculations and situations where precision loss
 * is unacceptable. BCMath operates slower than native PHP math but guarantees
 * deterministic, accurate results for decimal operations.
 *
 * Requires the BCMath PHP extension to be installed and enabled. Throws an exception
 * during construction if the extension is unavailable.
 *
 * @author Brian Faust <brian@cline.sh>
 *
 * @psalm-immutable
 *
 * @see https://www.php.net/manual/en/book.bc.php BCMath extension documentation
 */
final readonly class BCMathAdapter implements MathAdapter
{
    /**
     * Create a new BCMath adapter with specified precision.
     *
     * @param int $scale Number of decimal places to maintain in calculations (default: 10).
     *                   This scale applies to all arithmetic operations and determines the
     *                   precision of intermediate and final results. Higher values increase
     *                   accuracy but may impact performance.
     *
     * @throws InvalidArgumentException When the BCMath extension is not loaded or available
     */
    public function __construct(
        private int $scale = 10,
    ) {
        throw_if(
            !extension_loaded('bcmath'),
            InvalidArgumentException::class,
            'BCMath extension is required for BCMathAdapter',
        );
    }

    /**
     * Add two numbers with BCMath precision.
     *
     * @param  float|int|string $a The first addend
     * @param  float|int|string $b The second addend
     * @return string           The sum as a numeric string
     */
    public function add(int|float|string $a, int|float|string $b): string
    {
        return bcadd($this->toString($a), $this->toString($b), $this->scale);
    }

    /**
     * Subtract one number from another with BCMath precision.
     *
     * @param  float|int|string $a The minuend
     * @param  float|int|string $b The subtrahend
     * @return string           The difference as a numeric string
     */
    public function subtract(int|float|string $a, int|float|string $b): string
    {
        return bcsub($this->toString($a), $this->toString($b), $this->scale);
    }

    /**
     * Multiply two numbers with BCMath precision.
     *
     * @param  float|int|string $a The first factor
     * @param  float|int|string $b The second factor
     * @return string           The product as a numeric string
     */
    public function multiply(int|float|string $a, int|float|string $b): string
    {
        return bcmul($this->toString($a), $this->toString($b), $this->scale);
    }

    /**
     * Divide one number by another with BCMath precision.
     *
     * @param  float|int|string $a The dividend
     * @param  float|int|string $b The divisor
     * @return string           The quotient as a numeric string
     */
    public function divide(int|float|string $a, int|float|string $b): string
    {
        return bcdiv($this->toString($a), $this->toString($b), $this->scale);
    }

    /**
     * Calculate modulo with BCMath precision.
     *
     * @param  float|int|string $a The dividend
     * @param  float|int|string $b The divisor
     * @return string           The remainder as a numeric string
     */
    public function mod(int|float|string $a, int|float|string $b): string
    {
        return bcmod($this->toString($a), $this->toString($b), $this->scale);
    }

    /**
     * Calculate the absolute value using BCMath precision.
     *
     * @param  float|int|string $value The number to get the absolute value of
     * @return string           The absolute value as a numeric string
     *
     * @phpstan-return numeric-string
     */
    public function abs(int|float|string $value): string
    {
        $val = $this->toString($value);

        return bccomp($val, '0', $this->scale) < 0 ? bcmul($val, '-1', $this->scale) : $val;
    }

    /**
     * Round up to the nearest integer using BCMath precision.
     *
     * For positive numbers with fractional parts, returns the next higher integer.
     * For negative numbers and whole numbers, returns the integer part unchanged.
     *
     * @param  float|int|string $value The number to round up
     * @return string           The ceiling value as a numeric string
     */
    public function ceil(int|float|string $value): string
    {
        $val = $this->toString($value);
        $int = (string) $this->integerPart($val);
        $frac = $this->fractionalPart($val);

        if (bccomp($frac, $this->toString(0), $this->scale) > 0 && bccomp($val, $this->toString(0), $this->scale) > 0) {
            return bcadd($int, '1', 0);
        }

        return $int;
    }

    /**
     * Round down to the nearest integer using BCMath precision.
     *
     * For positive numbers and whole numbers, returns the integer part unchanged.
     * For negative numbers with fractional parts, returns the next lower integer.
     *
     * @param  float|int|string $value The number to round down
     * @return string           The floor value as a numeric string
     */
    public function floor(int|float|string $value): string
    {
        $val = $this->toString($value);
        $int = (string) $this->integerPart($val);
        $frac = $this->fractionalPart($val);

        if (bccomp($frac, '0', $this->scale) > 0 && bccomp($val, '0', $this->scale) < 0) {
            return bcsub($int, '1', 0);
        }

        return $int;
    }

    /**
     * Round a number to specified precision using a rounding mode.
     *
     * Supports all PHP RoundingMode enum values for flexible rounding strategies.
     * The implementation scales the value, applies the rounding mode, then scales back.
     *
     * @param  float|int|string  $value     The number to round
     * @param  int               $precision Number of decimal places to round to
     * @param  null|RoundingMode $mode      The rounding strategy; defaults to HalfAwayFromZero
     * @return string            The rounded value as a numeric string
     */
    public function round(int|float|string $value, int $precision, ?RoundingMode $mode): string
    {
        $mode ??= RoundingMode::HalfAwayFromZero;
        $val = $this->toString($value);

        $multiplier = bcpow('10', (string) $precision, 0);
        $scaled = bcmul($val, $multiplier, $this->scale);

        $rounded = match ($mode) {
            RoundingMode::AwayFromZero => $this->roundAwayFromZero($scaled),
            RoundingMode::TowardsZero => $this->roundTowardsZero($scaled),
            RoundingMode::PositiveInfinity => $this->roundPositiveInfinity($scaled),
            RoundingMode::NegativeInfinity => $this->roundNegativeInfinity($scaled),
            RoundingMode::HalfAwayFromZero => $this->roundHalfAwayFromZero($scaled),
            RoundingMode::HalfTowardsZero => $this->roundHalfTowardsZero($scaled),
            RoundingMode::HalfEven => $this->roundHalfEven($scaled),
            RoundingMode::HalfOdd => $this->roundHalfOdd($scaled),
        };

        return bcdiv($rounded, $multiplier, $precision);
    }

    /**
     * Raise a number to a power using BCMath precision.
     *
     * @param  float|int|string $base     The base number
     * @param  float|int        $exponent The exponent to raise the base to
     * @return string           The result of base raised to exponent as a numeric string
     */
    public function power(int|float|string $base, int|float $exponent): string
    {
        return bcpow($this->toString($base), $this->toString($exponent), $this->scale);
    }

    /**
     * Calculate the square root using BCMath precision.
     *
     * @param  float|int|string $value The number to calculate the square root of
     * @return string           The square root as a numeric string
     */
    public function sqrt(int|float|string $value): string
    {
        return bcsqrt($this->toString($value), $this->scale);
    }

    /**
     * Compare two numbers using BCMath precision.
     *
     * @param  float|int|string $a The first number to compare
     * @param  float|int|string $b The second number to compare
     * @return int              Returns 1 if $a > $b, -1 if $a < $b, or 0 if $a == $b
     */
    public function compare(int|float|string $a, int|float|string $b): int
    {
        return bccomp($this->toString($a), $this->toString($b), $this->scale);
    }

    /**
     * Extract the integer part of a number.
     *
     * Truncates toward zero by removing the fractional portion. Handles both
     * string representations with decimal points and whole numbers.
     *
     * @param  float|int|string $value The number to extract the integer part from
     * @return int              The whole number portion
     */
    public function integerPart(int|float|string $value): int
    {
        $val = $this->toString($value);

        if (str_contains($val, '.')) {
            [$integer] = explode('.', $val, 2);

            return (int) $integer;
        }

        return (int) $val;
    }

    /**
     * Extract the fractional part as an absolute value using BCMath precision.
     *
     * Returns the decimal portion of the number, always as a positive value
     * between 0 and 1 regardless of the original number's sign.
     *
     * @param  float|int|string $value The number to extract the fractional part from
     * @return string           The decimal portion as a numeric string (absolute value)
     *
     * @phpstan-return numeric-string
     */
    public function fractionalPart(int|float|string $value): string
    {
        $val = $this->toString($value);
        $int = $this->toString($this->integerPart($val));
        $frac = bcsub($val, $int, $this->scale);

        return $this->abs($frac);
    }

    /**
     * Negate a number using BCMath precision.
     *
     * @param  float|int|string $value The number to negate
     * @return string           The negated value as a numeric string
     */
    public function negate(int|float|string $value): string
    {
        return bcmul($this->toString($value), '-1', $this->scale);
    }

    /**
     * Get the minimum of two numbers using BCMath precision.
     *
     * @param  float|int|string $a The first number to compare
     * @param  float|int|string $b The second number to compare
     * @return string           The smaller of the two numbers as a numeric string
     */
    public function min(int|float|string $a, int|float|string $b): string
    {
        return bccomp($this->toString($a), $this->toString($b), $this->scale) <= 0
            ? $this->toString($a)
            : $this->toString($b);
    }

    /**
     * Get the maximum of two numbers using BCMath precision.
     *
     * @param  float|int|string $a The first number to compare
     * @param  float|int|string $b The second number to compare
     * @return string           The larger of the two numbers as a numeric string
     */
    public function max(int|float|string $a, int|float|string $b): string
    {
        return bccomp($this->toString($a), $this->toString($b), $this->scale) >= 0
            ? $this->toString($a)
            : $this->toString($b);
    }

    /**
     * Convert a numeric value to string representation.
     *
     * Internal helper method to normalize input types for BCMath functions,
     * which require string operands.
     *
     * @param  float|int|string $value The numeric value to convert
     * @return string           The value as a string
     *
     * @phpstan-return numeric-string
     */
    private function toString(int|float|string $value): string
    {
        return (string) $value; // @phpstan-ignore return.type
    }

    /**
     * @param numeric-string $value
     *
     * @phpstan-return numeric-string
     */
    private function roundAwayFromZero(string $value): string
    {
        $int = $this->integerPart($value);
        $frac = $this->fractionalPart($value);

        if (bccomp($frac, $this->toString(0), $this->scale) > 0) {
            return bccomp($value, $this->toString(0), $this->scale) >= 0
                ? bcadd($this->toString($int), '1', 0)
                : bcsub($this->toString($int), '1', 0);
        }

        return $this->toString($int);
    }

    /**
     * @param numeric-string $value
     *
     * @phpstan-return numeric-string
     */
    private function roundTowardsZero(string $value): string
    {
        return $this->toString($this->integerPart($value));
    }

    /**
     * @param numeric-string $value
     *
     * @phpstan-return numeric-string
     */
    private function roundPositiveInfinity(string $value): string
    {
        $int = $this->integerPart($value);
        $frac = $this->fractionalPart($value);

        if (bccomp($frac, $this->toString(0), $this->scale) > 0 && bccomp($value, $this->toString(0), $this->scale) > 0) {
            return bcadd($this->toString($int), '1', 0);
        }

        return $this->toString($int);
    }

    /**
     * @param numeric-string $value
     *
     * @phpstan-return numeric-string
     */
    private function roundNegativeInfinity(string $value): string
    {
        $int = $this->integerPart($value);
        $frac = $this->fractionalPart($value);

        if (bccomp($frac, $this->toString(0), $this->scale) > 0 && bccomp($value, $this->toString(0), $this->scale) < 0) {
            return bcsub($this->toString($int), '1', 0);
        }

        return $this->toString($int);
    }

    /**
     * @param numeric-string $value
     *
     * @phpstan-return numeric-string
     */
    private function roundHalfAwayFromZero(string $value): string
    {
        $int = $this->integerPart($value);
        $frac = $this->fractionalPart($value);

        if (bccomp($frac, $this->toString(0.5), $this->scale) >= 0) {
            return bccomp($value, $this->toString(0), $this->scale) >= 0
                ? bcadd($this->toString($int), '1', 0)
                : bcsub($this->toString($int), '1', 0);
        }

        return $this->toString($int);
    }

    /**
     * @param numeric-string $value
     *
     * @phpstan-return numeric-string
     */
    private function roundHalfTowardsZero(string $value): string
    {
        $int = $this->integerPart($value);
        $frac = $this->fractionalPart($value);

        if (bccomp($frac, $this->toString(0.5), $this->scale) > 0) {
            return bccomp($value, $this->toString(0), $this->scale) >= 0
                ? bcadd($this->toString($int), '1', 0)
                : bcsub($this->toString($int), '1', 0);
        }

        return $this->toString($int);
    }

    /**
     * @param numeric-string $value
     *
     * @phpstan-return numeric-string
     */
    private function roundHalfEven(string $value): string
    {
        $int = $this->integerPart($value);
        $frac = $this->fractionalPart($value);
        $cmp = bccomp($frac, $this->toString(0.5), $this->scale);

        if ($cmp > 0) {
            return bccomp($value, $this->toString(0), $this->scale) >= 0
                ? bcadd($this->toString($int), '1', 0)
                : bcsub($this->toString($int), '1', 0);
        }

        if ($cmp === 0) {
            return abs($int) % 2 === 0 ? $this->toString($int) : (bccomp($value, $this->toString(0), $this->scale) >= 0
                ? bcadd($this->toString($int), '1', 0)
                : bcsub($this->toString($int), '1', 0));
        }

        return $this->toString($int);
    }

    /**
     * @param numeric-string $value
     *
     * @phpstan-return numeric-string
     */
    private function roundHalfOdd(string $value): string
    {
        $int = $this->integerPart($value);
        $frac = $this->fractionalPart($value);
        $cmp = bccomp($frac, $this->toString(0.5), $this->scale);

        if ($cmp > 0) {
            return bccomp($value, $this->toString(0), $this->scale) >= 0
                ? bcadd($this->toString($int), '1', 0)
                : bcsub($this->toString($int), '1', 0);
        }

        if ($cmp === 0) {
            return abs($int) % 2 === 1 ? $this->toString($int) : (bccomp($value, $this->toString(0), $this->scale) >= 0
                ? bcadd($this->toString($int), '1', 0)
                : bcsub($this->toString($int), '1', 0));
        }

        return $this->toString($int);
    }
}
