<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\Numerus\Adapters;

use Cline\Numerus\Contracts\MathAdapter;
use GMP;
use InvalidArgumentException;
use RoundingMode;

use const STR_PAD_LEFT;

use function explode;
use function extension_loaded;
use function gmp_abs;
use function gmp_add;
use function gmp_cmp;
use function gmp_div_q;
use function gmp_init;
use function gmp_mod;
use function gmp_mul;
use function gmp_neg;
use function gmp_pow;
use function gmp_sqrt;
use function gmp_strval;
use function gmp_sub;
use function is_int;
use function mb_rtrim;
use function mb_str_pad;
use function mb_substr;
use function round;
use function str_contains;
use function str_repeat;
use function str_starts_with;
use function throw_if;

/**
 * Integer-based mathematical adapter using the GMP extension.
 *
 * Provides high-performance arbitrary-precision arithmetic operations using PHP's GMP
 * (GNU Multiple Precision) extension. GMP operates on integers and uses an internal
 * scaling mechanism to simulate decimal arithmetic, making it faster than BCMath for
 * many operations while maintaining good precision.
 *
 * The adapter internally multiplies values by a scale factor (10^scale) to represent
 * decimals as integers, performs integer arithmetic, then converts back. This approach
 * offers excellent performance for financial calculations and scenarios requiring both
 * speed and precision, though it's limited to operations that can be expressed using
 * integer mathematics.
 *
 * Requires the GMP PHP extension to be installed and enabled. Throws an exception
 * during construction if the extension is unavailable. Note that power operations
 * only support non-negative integer exponents due to GMP's integer-only nature.
 *
 * @author Brian Faust <brian@cline.sh>
 *
 * @psalm-immutable
 *
 * @see https://www.php.net/manual/en/book.gmp.php GMP extension documentation
 */
final readonly class GMPAdapter implements MathAdapter
{
    /**
     * The scaling factor used to represent decimals as integers.
     *
     * Calculated as 10^scale and used to convert decimal values to scaled integers
     * for GMP operations, then convert results back to decimal representation.
     */
    private int $scaleFactor;

    /**
     * Create a new GMP adapter with specified precision.
     *
     * @param int $scale Number of decimal places to maintain in calculations (default: 10).
     *                   This scale determines the precision of operations by defining
     *                   the internal scaling factor (10^scale). Higher values increase
     *                   precision but may impact performance and require more memory.
     *
     * @throws InvalidArgumentException When the GMP extension is not loaded or available
     */
    public function __construct(
        private int $scale = 10,
    ) {
        throw_if(
            !extension_loaded('gmp'),
            InvalidArgumentException::class,
            'GMP extension is required for GMPAdapter',
        );

        $this->scaleFactor = 10 ** $scale;
    }

    /**
     * Add two numbers using GMP integer arithmetic.
     *
     * @param  float|int|string $a The first addend
     * @param  float|int|string $b The second addend
     * @return string           The sum as a string
     */
    public function add(int|float|string $a, int|float|string $b): string
    {
        $gmpA = $this->toGMP($a);
        $gmpB = $this->toGMP($b);

        return $this->fromGMP(gmp_add($gmpA, $gmpB));
    }

    /**
     * Subtract one number from another using GMP integer arithmetic.
     *
     * @param  float|int|string $a The minuend
     * @param  float|int|string $b The subtrahend
     * @return string           The difference as a string
     */
    public function subtract(int|float|string $a, int|float|string $b): string
    {
        $gmpA = $this->toGMP($a);
        $gmpB = $this->toGMP($b);

        return $this->fromGMP(gmp_sub($gmpA, $gmpB));
    }

    /**
     * Multiply two numbers using GMP integer arithmetic.
     *
     * After multiplication, divides by the scale factor to maintain proper decimal precision.
     *
     * @param  float|int|string $a The first factor
     * @param  float|int|string $b The second factor
     * @return string           The product as a string
     */
    public function multiply(int|float|string $a, int|float|string $b): string
    {
        $gmpA = $this->toGMP($a);
        $gmpB = $this->toGMP($b);
        $result = gmp_mul($gmpA, $gmpB);

        return $this->fromGMP(gmp_div_q($result, gmp_init((string) $this->scaleFactor)));
    }

    /**
     * Divide one number by another using GMP integer arithmetic.
     *
     * Scales the dividend before division to maintain decimal precision in the result.
     *
     * @param  float|int|string $a The dividend
     * @param  float|int|string $b The divisor
     * @return string           The quotient as a string
     */
    public function divide(int|float|string $a, int|float|string $b): string
    {
        $gmpA = $this->toGMP($a);
        $gmpB = $this->toGMP($b);
        $scaled = gmp_mul($gmpA, gmp_init((string) $this->scaleFactor));

        return $this->fromGMP(gmp_div_q($scaled, $gmpB));
    }

    /**
     * Calculate modulo using GMP integer arithmetic.
     *
     * @param  float|int|string $a The dividend
     * @param  float|int|string $b The divisor
     * @return string           The remainder as a string
     */
    public function mod(int|float|string $a, int|float|string $b): string
    {
        $gmpA = $this->toGMP($a);
        $gmpB = $this->toGMP($b);

        return $this->fromGMP(gmp_mod($gmpA, $gmpB));
    }

    /**
     * Calculate absolute value using GMP integer arithmetic.
     *
     * @param  float|int|string $value The number to get the absolute value of
     * @return string           The absolute value as a string
     */
    public function abs(int|float|string $value): string
    {
        return $this->fromGMP(gmp_abs($this->toGMP($value)));
    }

    /**
     * Round up to the nearest integer using GMP integer arithmetic.
     *
     * @param  float|int|string $value The number to round up
     * @return string           The ceiling value as a string
     */
    public function ceil(int|float|string $value): string
    {
        $gmp = $this->toGMP($value);
        $int = gmp_div_q($gmp, gmp_init((string) $this->scaleFactor));
        $remainder = gmp_mod($gmp, gmp_init((string) $this->scaleFactor));

        if (gmp_cmp($remainder, gmp_init('0')) > 0 && gmp_cmp($gmp, gmp_init('0')) > 0) {
            return gmp_strval(gmp_add($int, gmp_init('1')));
        }

        return gmp_strval($int);
    }

    /**
     * Round down to the nearest integer using GMP integer arithmetic.
     *
     * @param  float|int|string $value The number to round down
     * @return string           The floor value as a string
     */
    public function floor(int|float|string $value): string
    {
        $gmp = $this->toGMP($value);
        $int = gmp_div_q($gmp, gmp_init((string) $this->scaleFactor));
        $remainder = gmp_mod($gmp, gmp_init((string) $this->scaleFactor));

        if (gmp_cmp($remainder, gmp_init('0')) > 0 && gmp_cmp($gmp, gmp_init('0')) < 0) {
            return gmp_strval(gmp_sub($int, gmp_init('1')));
        }

        return gmp_strval($int);
    }

    /**
     * Round to specified precision using native PHP rounding.
     *
     * Converts the GMP value to float for rounding since GMP doesn't provide native
     * rounding mode support. This may introduce minor precision differences.
     *
     * @param  float|int|string  $value     The number to round
     * @param  int               $precision Number of decimal places to round to
     * @param  null|RoundingMode $mode      The rounding strategy; defaults to HalfAwayFromZero
     * @return string            The rounded value as a string
     */
    public function round(int|float|string $value, int $precision, ?RoundingMode $mode): string
    {
        $mode ??= RoundingMode::HalfAwayFromZero;
        $val = (float) $this->fromGMP($this->toGMP($value));

        return (string) round($val, $precision, $mode);
    }

    /**
     * Raise a number to a power using GMP integer arithmetic.
     *
     * Only supports non-negative integer exponents due to GMP's integer-only nature.
     * Adjusts the result by dividing by the scale factor to maintain decimal precision.
     *
     * @param float|int|string $base     The base number
     * @param float|int        $exponent The exponent to raise the base to
     *
     * @throws InvalidArgumentException When exponent is not a non-negative integer
     *
     * @return string The result of base raised to exponent as a string
     */
    public function power(int|float|string $base, int|float $exponent): string
    {
        throw_if(!is_int($exponent) || $exponent < 0, InvalidArgumentException::class, 'GMPAdapter only supports non-negative integer exponents');

        $gmpBase = $this->toGMP($base);
        $result = gmp_pow($gmpBase, $exponent);

        for ($i = 0; $i < $exponent - 1; ++$i) {
            $result = gmp_div_q($result, gmp_init((string) $this->scaleFactor));
        }

        return $this->fromGMP($result);
    }

    /**
     * Calculate square root using GMP integer arithmetic.
     *
     * Scales the value before taking the square root to maintain decimal precision.
     *
     * @param  float|int|string $value The number to calculate the square root of
     * @return string           The square root as a string
     */
    public function sqrt(int|float|string $value): string
    {
        $gmp = $this->toGMP($value);
        $scaled = gmp_mul($gmp, gmp_init((string) $this->scaleFactor));
        $sqrt = gmp_sqrt($scaled);

        return $this->fromGMP($sqrt);
    }

    /**
     * Compare two numbers using GMP integer arithmetic.
     *
     * @param  float|int|string $a The first number to compare
     * @param  float|int|string $b The second number to compare
     * @return int              Returns 1 if $a > $b, -1 if $a < $b, or 0 if $a == $b
     */
    public function compare(int|float|string $a, int|float|string $b): int
    {
        return gmp_cmp($this->toGMP($a), $this->toGMP($b));
    }

    /**
     * Extract the integer part of a number.
     *
     * Divides by the scale factor to remove the decimal portion.
     *
     * @param  float|int|string $value The number to extract the integer part from
     * @return int              The whole number portion
     */
    public function integerPart(int|float|string $value): int
    {
        $gmp = $this->toGMP($value);
        $int = gmp_div_q($gmp, gmp_init((string) $this->scaleFactor));

        return (int) gmp_strval($int);
    }

    /**
     * Extract the fractional part as an absolute value using GMP integer arithmetic.
     *
     * Subtracts the integer portion from the scaled value to isolate the fractional part.
     *
     * @param  float|int|string $value The number to extract the fractional part from
     * @return string           The decimal portion as a string (absolute value)
     */
    public function fractionalPart(int|float|string $value): string
    {
        $gmp = $this->toGMP($value);
        $int = gmp_mul(
            gmp_div_q($gmp, gmp_init((string) $this->scaleFactor)),
            gmp_init((string) $this->scaleFactor),
        );
        $frac = gmp_sub($gmp, $int);

        return $this->fromGMP(gmp_abs($frac));
    }

    /**
     * Negate a number using GMP integer arithmetic.
     *
     * @param  float|int|string $value The number to negate
     * @return string           The negated value as a string
     */
    public function negate(int|float|string $value): string
    {
        return $this->fromGMP(gmp_neg($this->toGMP($value)));
    }

    /**
     * Get the minimum of two numbers using GMP integer arithmetic.
     *
     * @param  float|int|string $a The first number to compare
     * @param  float|int|string $b The second number to compare
     * @return string           The smaller of the two numbers as a string
     */
    public function min(int|float|string $a, int|float|string $b): string
    {
        return gmp_cmp($this->toGMP($a), $this->toGMP($b)) <= 0
            ? $this->fromGMP($this->toGMP($a))
            : $this->fromGMP($this->toGMP($b));
    }

    /**
     * Get the maximum of two numbers using GMP integer arithmetic.
     *
     * @param  float|int|string $a The first number to compare
     * @param  float|int|string $b The second number to compare
     * @return string           The larger of the two numbers as a string
     */
    public function max(int|float|string $a, int|float|string $b): string
    {
        return gmp_cmp($this->toGMP($a), $this->toGMP($b)) >= 0
            ? $this->fromGMP($this->toGMP($a))
            : $this->fromGMP($this->toGMP($b));
    }

    /**
     * Convert a numeric value to GMP object with scaling.
     *
     * Converts the value to a string, separates integer and decimal parts, then scales
     * by the scale factor to create an integer representation suitable for GMP operations.
     *
     * @param  float|int|string $value The numeric value to convert
     * @return GMP              The scaled value as a GMP object
     */
    private function toGMP(int|float|string $value): GMP
    {
        $strValue = (string) $value;

        if (str_contains($strValue, '.')) {
            [$int, $dec] = explode('.', $strValue, 2);
            $dec = mb_str_pad($dec, $this->scale, '0');
            $dec = mb_substr($dec, 0, $this->scale);
            $scaled = $int.$dec;
        } else {
            $scaled = $strValue.str_repeat('0', $this->scale);
        }

        return gmp_init($scaled);
    }

    /**
     * Convert a GMP object back to decimal string representation.
     *
     * Reverses the scaling process by separating the scaled integer into integer and
     * fractional parts based on the scale factor. Handles negative values and removes
     * trailing zeros from the fractional part for clean output.
     *
     * @param  GMP    $gmp The GMP object to convert
     * @return string The decimal string representation
     */
    private function fromGMP(GMP $gmp): string
    {
        $str = gmp_strval($gmp);
        $isNegative = str_starts_with($str, '-');

        if ($isNegative) {
            $str = mb_substr($str, 1);
        }

        $str = mb_str_pad($str, $this->scale + 1, '0', STR_PAD_LEFT);
        $intPart = mb_substr($str, 0, -$this->scale);
        $fracPart = mb_substr($str, -$this->scale);

        $fracPart = mb_rtrim($fracPart, '0');

        $result = $fracPart === '' ? $intPart : $intPart.'.'.$fracPart;

        return $isNegative ? '-'.$result : $result;
    }
}
