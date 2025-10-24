<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\Numerus\Adapters;

use Cline\Numerus\Contracts\MathAdapter;
use RoundingMode;

use function abs;
use function ceil;
use function floor;
use function is_string;
use function max;
use function min;
use function round;
use function sqrt;

/**
 * Native PHP mathematical adapter using built-in operators.
 *
 * Provides mathematical operations using PHP's native arithmetic operators and functions.
 * This adapter offers the fastest performance among all adapters but is subject to PHP's
 * standard floating-point precision limitations and potential rounding errors inherent
 * to IEEE 754 double-precision arithmetic.
 *
 * Best suited for scenarios where performance is critical and the precision requirements
 * are within the capabilities of standard PHP float operations (approximately 15-17
 * significant decimal digits). Not recommended for financial calculations requiring
 * exact decimal arithmetic or when working with very large numbers.
 *
 * No PHP extensions are required, making this adapter universally available. It serves
 * as the fallback option when neither BCMath nor GMP extensions are available.
 *
 * @author Brian Faust <brian@cline.sh>
 *
 * @psalm-immutable
 */
final readonly class NativeMathAdapter implements MathAdapter
{
    /**
     * Add two numbers using native PHP arithmetic.
     *
     * @param  float|int|string $a The first addend
     * @param  float|int|string $b The second addend
     * @return float|int        The sum
     */
    public function add(int|float|string $a, int|float|string $b): int|float
    {
        return $this->toNumber($a) + $this->toNumber($b);
    }

    /**
     * Subtract one number from another using native PHP arithmetic.
     *
     * @param  float|int|string $a The minuend
     * @param  float|int|string $b The subtrahend
     * @return float|int        The difference
     */
    public function subtract(int|float|string $a, int|float|string $b): int|float
    {
        return $this->toNumber($a) - $this->toNumber($b);
    }

    /**
     * Multiply two numbers using native PHP arithmetic.
     *
     * @param  float|int|string $a The first factor
     * @param  float|int|string $b The second factor
     * @return float|int        The product
     */
    public function multiply(int|float|string $a, int|float|string $b): int|float
    {
        return $this->toNumber($a) * $this->toNumber($b);
    }

    /**
     * Divide one number by another using native PHP arithmetic.
     *
     * @param  float|int|string $a The dividend
     * @param  float|int|string $b The divisor
     * @return float|int        The quotient
     */
    public function divide(int|float|string $a, int|float|string $b): int|float
    {
        return $this->toNumber($a) / $this->toNumber($b);
    }

    /**
     * Calculate modulo using native PHP arithmetic.
     *
     * @param  float|int|string $a The dividend
     * @param  float|int|string $b The divisor
     * @return int              The remainder
     */
    public function mod(int|float|string $a, int|float|string $b): int
    {
        return $this->toNumber($a) % $this->toNumber($b);
    }

    /**
     * Calculate absolute value using native PHP function.
     *
     * @param  float|int|string $value The number to get the absolute value of
     * @return float|int        The absolute value
     */
    public function abs(int|float|string $value): int|float
    {
        return abs($this->toNumber($value));
    }

    /**
     * Round up to the nearest integer using native PHP function.
     *
     * @param  float|int|string $value The number to round up
     * @return float            The ceiling value
     */
    public function ceil(int|float|string $value): float
    {
        return ceil($this->toNumber($value));
    }

    /**
     * Round down to the nearest integer using native PHP function.
     *
     * @param  float|int|string $value The number to round down
     * @return float            The floor value
     */
    public function floor(int|float|string $value): float
    {
        return floor($this->toNumber($value));
    }

    /**
     * Round to specified precision using native PHP function.
     *
     * @param  float|int|string  $value     The number to round
     * @param  int               $precision Number of decimal places to round to
     * @param  null|RoundingMode $mode      The rounding strategy; defaults to HalfAwayFromZero
     * @return float             The rounded value
     */
    public function round(int|float|string $value, int $precision, ?RoundingMode $mode): float
    {
        $mode ??= RoundingMode::HalfAwayFromZero;

        return round($this->toNumber($value), $precision, $mode);
    }

    /**
     * Raise a number to a power using native PHP operator.
     *
     * @param  float|int|string $base     The base number
     * @param  float|int        $exponent The exponent to raise the base to
     * @return float|int        The result of base raised to exponent
     */
    public function power(int|float|string $base, int|float $exponent): int|float
    {
        return $this->toNumber($base) ** $exponent;
    }

    /**
     * Calculate square root using native PHP function.
     *
     * @param  float|int|string $value The number to calculate the square root of
     * @return float            The square root
     */
    public function sqrt(int|float|string $value): float
    {
        return sqrt($this->toNumber($value));
    }

    /**
     * Compare two numbers using native PHP comparison.
     *
     * @param  float|int|string $a The first number to compare
     * @param  float|int|string $b The second number to compare
     * @return int              Returns 1 if $a > $b, -1 if $a < $b, or 0 if $a == $b
     */
    public function compare(int|float|string $a, int|float|string $b): int
    {
        $numA = $this->toNumber($a);
        $numB = $this->toNumber($b);

        if ($numA > $numB) {
            return 1;
        }

        if ($numA < $numB) {
            return -1;
        }

        return 0;
    }

    /**
     * Extract the integer part of a number using type casting.
     *
     * @param  float|int|string $value The number to extract the integer part from
     * @return int              The whole number portion
     */
    public function integerPart(int|float|string $value): int
    {
        return (int) $this->toNumber($value);
    }

    /**
     * Extract the fractional part as an absolute value using native arithmetic.
     *
     * @param  float|int|string $value The number to extract the fractional part from
     * @return float|int        The decimal portion (absolute value)
     */
    public function fractionalPart(int|float|string $value): int|float
    {
        $num = $this->toNumber($value);

        return abs($num - (int) $num);
    }

    /**
     * Negate a number using native PHP arithmetic.
     *
     * @param  float|int|string $value The number to negate
     * @return float|int        The negated value
     */
    public function negate(int|float|string $value): int|float
    {
        return -$this->toNumber($value);
    }

    /**
     * Get the minimum of two numbers using native PHP function.
     *
     * @param  float|int|string $a The first number to compare
     * @param  float|int|string $b The second number to compare
     * @return float|int        The smaller of the two numbers
     */
    public function min(int|float|string $a, int|float|string $b): int|float
    {
        return min($this->toNumber($a), $this->toNumber($b));
    }

    /**
     * Get the maximum of two numbers using native PHP function.
     *
     * @param  float|int|string $a The first number to compare
     * @param  float|int|string $b The second number to compare
     * @return float|int        The larger of the two numbers
     */
    public function max(int|float|string $a, int|float|string $b): int|float
    {
        return max($this->toNumber($a), $this->toNumber($b));
    }

    /**
     * Convert a value to numeric type (int or float).
     *
     * Normalizes input types to native PHP numbers. For string inputs, attempts to
     * return an integer if the value has no fractional part, otherwise returns a float.
     *
     * @param  float|int|string $value The value to convert
     * @return float|int        The numeric value
     */
    private function toNumber(int|float|string $value): int|float
    {
        if (is_string($value)) {
            $float = (float) $value;

            return floor($float) === $float ? (int) $float : $float;
        }

        return $value;
    }
}
