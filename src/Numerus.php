<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\Numerus;

use Brick\Math\BigDecimal;
use Brick\Math\Exception\IntegerOverflowException;
use Brick\Math\RoundingMode as BrickRoundingMode;
use Illuminate\Support\Number;
use InvalidArgumentException;
use RoundingMode;
use Stringable;

use function abs;
use function count;
use function is_int;
use function is_string;
use function sprintf;
use function sqrt;
use function throw_if;

/**
 * Immutable value object for numeric operations with Laravel Number integration.
 *
 * Provides a fluent API for mathematical operations, comparisons, and
 * locale-aware formatting. All operations return new instances, preserving
 * immutability throughout the calculation chain.
 *
 * This class wraps the Brick\Math\BigDecimal library for arbitrary-precision
 * arithmetic while providing a Laravel-friendly interface with seamless
 * integration to Laravel's Number helper for locale-aware formatting.
 *
 * ```php
 * $result = numerus(100)
 *     ->addPercent(20)
 *     ->divideBy(2)
 *     ->round(2)
 *     ->format();
 * ```
 *
 * @author Brian Faust <brian@cline.sh>
 *
 * @see https://laravel.com/docs/12.x/helpers#numbers
 * @see https://github.com/brick/math
 *
 * @psalm-immutable
 *
 * @since 1.0.0
 */
final readonly class Numerus implements Stringable
{
    /**
     * Create a new immutable numeric value object.
     *
     * Private constructor enforces use of named constructors (create, parseInt, parseFloat)
     * to ensure consistent instance creation and maintain control over the public API.
     *
     * @param BigDecimal $value The numeric value stored as arbitrary-precision decimal
     */
    private function __construct(
        /**
         * The numeric value stored as arbitrary-precision decimal.
         */
        private BigDecimal $value,
    ) {}

    /**
     * Convert the numeric value to its string representation.
     *
     * Implements Stringable interface to enable automatic string conversion
     * in contexts requiring strings, such as echo statements or string
     * concatenation operations.
     *
     * @return string The numeric value as a string
     */
    public function __toString(): string
    {
        return $this->toString();
    }

    /**
     * Create a new Numerus instance from a numeric value or locale-aware string.
     *
     * Primary factory method for creating Numerus instances. Accepts integers,
     * floats, or locale-formatted strings that are automatically parsed into
     * numeric values. String parsing respects locale-specific formatting rules
     * for thousands separators and decimal points.
     *
     * When a string is provided, delegates to parseFloat() internally to handle
     * locale-aware parsing. The locale parameter determines which formatting
     * conventions to use: when null, uses the default locale set via useLocale(),
     * or falls back to the application's configured locale. Set a global default
     * locale once with useLocale() to avoid passing it repeatedly.
     *
     * ```php
     * // Creating from integers
     * Numerus::create(42);          // 42
     * Numerus::create(-100);        // -100
     *
     * // Creating from floats
     * Numerus::create(42.5);        // 42.5
     * Numerus::create(3.14159);     // 3.14159
     *
     * // Creating from strings with default locale (en_US)
     * Numerus::create('1,234.56');  // 1234.56 (thousands separator removed)
     * Numerus::create('42.50');     // 42.5
     *
     * // Creating from strings with explicit locale
     * Numerus::create('1.234,56', 'de_DE');  // 1234.56 (German format)
     * Numerus::create('1 234,56', 'fr_FR');  // 1234.56 (French format)
     *
     * // Setting default locale to avoid repetition
     * Numerus::useLocale('de_DE');
     * Numerus::create('1.234,56');  // 1234.56 (uses de_DE by default)
     * Numerus::create('999,99');    // 999.99 (uses de_DE by default)
     * ```
     *
     * @param float|int|string $value  The numeric value to wrap in a Numerus instance.
     *                                 Integers and floats are used directly. Strings
     *                                 are parsed as locale-formatted numbers, respecting
     *                                 culture-specific thousands separators and decimal
     *                                 point conventions (e.g., "1,234.56" in en_US vs
     *                                 "1.234,56" in de_DE).
     * @param null|string      $locale Optional BCP 47 locale identifier (e.g., 'en_US',
     *                                 'de_DE', 'fr_FR') for parsing string values. When
     *                                 null, uses the default locale configured via
     *                                 useLocale() or the application's locale setting.
     *                                 Only applies when $value is a string; ignored
     *                                 for numeric inputs.
     *
     * @throws InvalidArgumentException When a string value cannot be parsed as a valid
     *                                  number in the specified locale format. This occurs
     *                                  when the string contains invalid characters or
     *                                  doesn't match the locale's expected number format.
     *
     * @return self A new immutable Numerus instance containing the parsed or wrapped value
     *
     * @see parseFloat() The underlying method used for string parsing with locale support
     * @see useLocale() Set a global default locale to avoid passing it for every string
     */
    public static function create(int|float|string $value, ?string $locale = null): self
    {
        if (is_string($value)) {
            return self::parseFloat($value, $locale);
        }

        return new self(BigDecimal::of($value));
    }

    /**
     * Set the default locale for number formatting.
     *
     * Configures the global locale used for all number formatting operations
     * including currency, percentages, and human-readable output. This setting
     * persists for the duration of the request unless changed.
     *
     * @param string $locale BCP 47 locale identifier (e.g., 'en_US', 'de_DE', 'fr_FR')
     *                       used for formatting numbers, currencies, and percentages
     *
     * @see https://laravel.com/docs/12.x/helpers#method-number-use-locale
     */
    public static function useLocale(string $locale): void
    {
        Number::useLocale($locale);
    }

    /**
     * Set the default currency for currency formatting.
     *
     * Configures the global currency used for all currency formatting operations.
     * This setting persists for the duration of the request unless changed.
     *
     * @param string $currency ISO 4217 currency code (e.g., 'USD', 'EUR', 'GBP')
     *                         used as the default for currency formatting operations
     *
     * @see https://laravel.com/docs/12.x/helpers#method-number-use-currency
     */
    public static function useCurrency(string $currency): void
    {
        Number::useCurrency($currency);
    }

    /**
     * Get the default locale for number formatting.
     *
     * @return string The currently configured default locale identifier
     *
     * @see https://laravel.com/docs/12.x/helpers#method-number-default-locale
     */
    public static function defaultLocale(): string
    {
        return Number::defaultLocale();
    }

    /**
     * Get the default currency for currency formatting.
     *
     * @return string The currently configured default currency code
     *
     * @see https://laravel.com/docs/12.x/helpers#method-number-default-currency
     */
    public static function defaultCurrency(): string
    {
        return Number::defaultCurrency();
    }

    /**
     * Execute a callback with a temporary locale for number formatting.
     *
     * @param  string   $locale   BCP 47 locale identifier to use temporarily
     * @param  callable $callback Closure or callable to execute with the temporary locale
     * @return mixed    The return value from the callback execution
     *
     * @see https://laravel.com/docs/12.x/helpers#method-number-with-locale
     */
    public static function withLocale(string $locale, callable $callback): mixed
    {
        return Number::withLocale($locale, $callback);
    }

    /**
     * Execute a callback with a temporary currency for currency formatting.
     *
     * @param  string   $currency ISO 4217 currency code to use temporarily
     * @param  callable $callback Closure or callable to execute with the temporary currency
     * @return mixed    The return value from the callback execution
     *
     * @see https://laravel.com/docs/12.x/helpers#method-number-with-currency
     */
    public static function withCurrency(string $currency, callable $callback): mixed
    {
        return Number::withCurrency($currency, $callback);
    }

    /**
     * Parse a locale-aware string into an integer.
     *
     * Converts a locale-formatted string representation of an integer into a Numerus
     * instance. Handles locale-specific thousand separators automatically.
     *
     * @param string      $value  The locale-formatted string to parse (e.g., "1,234" in en_US)
     * @param null|string $locale Optional locale identifier; uses default if not specified
     *
     * @throws InvalidArgumentException When the string cannot be parsed as an integer
     *
     * @return self A new Numerus instance containing the parsed integer value
     *
     * @see https://laravel.com/docs/12.x/helpers#method-number-parse-int
     */
    public static function parseInt(string $value, ?string $locale = null): self
    {
        $result = Number::parseInt($value, $locale);

        throw_if($result === false, InvalidArgumentException::class, sprintf("Unable to parse '%s' as integer", $value));

        return new self(BigDecimal::of($result));
    }

    /**
     * Parse a locale-aware string into a float.
     *
     * Converts a locale-formatted string representation of a number into a Numerus
     * instance. Handles locale-specific thousand separators and decimal points.
     *
     * @param string      $value  The locale-formatted string to parse (e.g., "1,234.56" in en_US)
     * @param null|string $locale Optional locale identifier; uses default if not specified
     *
     * @throws InvalidArgumentException When the string cannot be parsed as a float
     *
     * @return self A new Numerus instance containing the parsed floating-point value
     *
     * @see https://laravel.com/docs/12.x/helpers#method-number-parse-float
     */
    public static function parseFloat(string $value, ?string $locale = null): self
    {
        $result = Number::parseFloat($value, $locale);

        throw_if($result === false, InvalidArgumentException::class, sprintf("Unable to parse '%s' as float", $value));

        return new self(BigDecimal::of($result));
    }

    /**
     * Generate an array of number pairs useful for pagination ranges.
     *
     * Creates tuples representing ranges like [1,10], [11,20] commonly used
     * for displaying paginated result sets or batch processing workflows.
     *
     * @param  int                         $limit  The upper boundary for the range generation
     * @param  int                         $step   The size of each range interval
     * @param  int                         $offset The starting number for the first range (default: 1)
     * @return array<int, array{int, int}> Array of tuples where each tuple contains [start, end]
     *
     * @see https://laravel.com/docs/12.x/helpers#method-number-pairs
     */
    public static function pairs(int $limit, int $step, int $offset = 1): array
    {
        /** @var array<int, array{int, int}> */
        return Number::pairs($limit, $step, $offset);
    }

    /**
     * Calculate the average (mean) of multiple values.
     *
     * @param array<float|int|self> $values Array of numeric values or Numerus instances
     *                                      to calculate the arithmetic mean from
     *
     * @throws InvalidArgumentException When the array is empty
     *
     * @return self A new Numerus instance containing the calculated average
     */
    public static function average(array $values): self
    {
        throw_if($values === [], InvalidArgumentException::class, 'Cannot calculate average of empty array');

        $sum = BigDecimal::zero();

        foreach ($values as $val) {
            $sum = $sum->plus($val instanceof self ? $val->value : BigDecimal::of($val));
        }

        return new self($sum->dividedBy(count($values), 10, BrickRoundingMode::HALF_UP));
    }

    /**
     * Calculate the sum of multiple values.
     *
     * @param  array<float|int|self> $values Array of numeric values or Numerus instances to sum
     * @return self                  A new Numerus instance containing the calculated sum
     */
    public static function sum(array $values): self
    {
        $sum = BigDecimal::zero();

        foreach ($values as $val) {
            $sum = $sum->plus($val instanceof self ? $val->value : BigDecimal::of($val));
        }

        return new self($sum);
    }

    /**
     * Get the raw numeric value.
     *
     * Returns the underlying value as a native PHP integer or float. Integer
     * is returned for whole numbers that fit within PHP's integer range,
     * otherwise a float is returned.
     *
     * @return float|int The encapsulated numeric value
     */
    public function value(): int|float
    {
        return $this->toNative($this->value);
    }

    /**
     * Extract the integer part of the number, discarding any fractional component.
     *
     * Truncates the value toward zero by removing the fractional portion,
     * equivalent to PHP's (int) cast. For negative numbers, truncation moves
     * toward zero rather than rounding down, so -12.99 becomes -12, not -13.
     *
     * ```php
     * numerus(12.34)->integerPart();   // 12
     * numerus(12.99)->integerPart();   // 12
     * numerus(-12.34)->integerPart();  // -12
     * numerus(-12.99)->integerPart();  // -12
     * numerus(0)->integerPart();       // 0
     * numerus(42)->integerPart();      // 42
     * ```
     *
     * @return int The whole number part with fractional portion removed
     *
     * @see fractionalPart() Retrieve the decimal portion as an absolute value
     */
    public function integerPart(): int
    {
        return $this->value->dividedBy(1, 0, BrickRoundingMode::DOWN)->toBigInteger()->toInt();
    }

    /**
     * Extract the fractional part of the number as an absolute value.
     *
     * Returns the decimal portion of the number, always as a positive value
     * between 0 and 1. The sign of the original number is discarded, so both
     * positive and negative values return the same fractional component. For
     * whole numbers, returns exactly 0.0 as a float.
     *
     * ```php
     * numerus(12.34)->fractionalPart();   // 0.34
     * numerus(12.99)->fractionalPart();   // 0.99
     * numerus(-12.34)->fractionalPart();  // 0.34 (absolute value)
     * numerus(-12.99)->fractionalPart();  // 0.99 (absolute value)
     * numerus(0)->fractionalPart();       // 0.0
     * numerus(42)->fractionalPart();      // 0.0
     * ```
     *
     * @return float The decimal portion as a non-negative value (0.0 ≤ result < 1.0)
     *
     * @see integerPart() Retrieve the whole number portion
     */
    public function fractionalPart(): float
    {
        $absValue = $this->value->abs();
        $integerPart = $absValue->dividedBy(1, 0, BrickRoundingMode::DOWN)->toBigInteger();

        return $absValue->minus($integerPart)->toFloat();
    }

    /**
     * Add a value to this number.
     *
     * @param  float|int|self $addend The value to add
     * @return self           A new Numerus instance with the sum
     */
    public function plus(int|float|self $addend): self
    {
        $value = $addend instanceof self ? $addend->value : BigDecimal::of($addend);

        return new self($this->value->plus($value));
    }

    /**
     * Subtract a value from this number.
     *
     * @param  float|int|self $subtrahend The value to subtract
     * @return self           A new Numerus instance with the difference
     */
    public function minus(int|float|self $subtrahend): self
    {
        $value = $subtrahend instanceof self ? $subtrahend->value : BigDecimal::of($subtrahend);

        return new self($this->value->minus($value));
    }

    /**
     * Multiply this number by a value.
     *
     * @param  float|int|self $multiplier The value to multiply by
     * @return self           A new Numerus instance with the product
     */
    public function multiplyBy(int|float|self $multiplier): self
    {
        $value = $multiplier instanceof self ? $multiplier->value : BigDecimal::of($multiplier);

        return new self($this->value->multipliedBy($value));
    }

    /**
     * Divide this number by a value.
     *
     * @param float|int|self $divisor The value to divide by
     *
     * @throws InvalidArgumentException When attempting to divide by zero
     *
     * @return self A new Numerus instance with the quotient
     */
    public function divideBy(int|float|self $divisor): self
    {
        $value = $divisor instanceof self ? $divisor->value : BigDecimal::of($divisor);

        throw_if($value->isZero(), InvalidArgumentException::class, 'Division by zero');

        return new self($this->value->dividedBy($value, 10, BrickRoundingMode::HALF_UP));
    }

    /**
     * Get the absolute value of this number.
     *
     * @return self A new Numerus instance with the absolute value
     */
    public function abs(): self
    {
        return new self($this->value->abs());
    }

    /**
     * Round this number up to the nearest integer.
     *
     * @return self A new Numerus instance with the ceiling value as an integer
     */
    public function ceil(): self
    {
        return new self($this->value->dividedBy(1, 0, BrickRoundingMode::CEILING));
    }

    /**
     * Round this number down to the nearest integer.
     *
     * @return self A new Numerus instance with the floor value as an integer
     */
    public function floor(): self
    {
        return new self($this->value->dividedBy(1, 0, BrickRoundingMode::FLOOR));
    }

    /**
     * Round this number to a specified precision using a given rounding mode.
     *
     * Provides flexible rounding with support for PHP's RoundingMode enum. When no mode
     * is specified, defaults to HalfAwayFromZero (standard rounding behavior).
     *
     * ```php
     * numerus(2.5)->round();                                   // 3
     * numerus(2.4)->round();                                   // 2
     * numerus(2.555)->round(2);                                // 2.56
     * numerus(2.5)->round(0, RoundingMode::HalfEven);          // 2 (banker's rounding)
     * numerus(-2.5)->round(0, RoundingMode::HalfAwayFromZero); // -3
     * ```
     *
     * @param  int               $precision Number of decimal places to round to (default: 0).
     *                                      Positive values round to decimal places, zero rounds
     *                                      to nearest integer, negative values round to tens,
     *                                      hundreds, etc.
     * @param  null|RoundingMode $mode      Rounding mode to use. When null, defaults to
     *                                      HalfAwayFromZero. See specific rounding mode methods
     *                                      for dedicated implementations.
     * @return self              A new Numerus instance with the rounded value
     */
    public function round(int $precision = 0, ?RoundingMode $mode = null): self
    {
        // HalfOdd requires custom implementation
        if ($mode === RoundingMode::HalfOdd) {
            $multiplier = BigDecimal::of(10)->power($precision);
            $shifted = $this->value->multipliedBy($multiplier);
            $floor = $shifted->dividedBy(1, 0, BrickRoundingMode::FLOOR);
            $fractional = $shifted->minus($floor);

            // Check if exactly at .5
            if ($fractional->isEqualTo('0.5')) {
                $rounded = $floor->toBigInteger()->isEven()
                    ? $floor->plus(1)  // Round up if even (to make it odd)
                    : $floor;           // Keep if already odd
            } else {
                $rounded = $shifted->toScale(0, BrickRoundingMode::HALF_UP);
            }

            return new self($rounded->dividedBy($multiplier, $precision, BrickRoundingMode::UNNECESSARY));
        }

        $roundingMode = $mode instanceof RoundingMode
            ? $this->toBrickRoundingMode($mode)
            : BrickRoundingMode::HALF_UP;

        return new self($this->value->toScale($precision, $roundingMode));
    }

    /**
     * Round away from zero at the specified precision.
     *
     * Always rounds toward the larger absolute value, regardless of sign.
     * Positive numbers round up, negative numbers round down (more negative).
     *
     * ```php
     * numerus(2.3)->roundAwayFromZero();  // 3
     * numerus(2.7)->roundAwayFromZero();  // 3
     * numerus(-2.3)->roundAwayFromZero(); // -3
     * numerus(-2.7)->roundAwayFromZero(); // -3
     * numerus(2.14)->roundAwayFromZero(1); // 2.2
     * ```
     *
     * @param  int  $precision Number of decimal places to round to (default: 0)
     * @return self A new Numerus instance with the rounded value
     */
    public function roundAwayFromZero(int $precision = 0): self
    {
        return new self($this->value->toScale($precision, BrickRoundingMode::UP));
    }

    /**
     * Round toward zero at the specified precision (truncate).
     *
     * Always rounds toward the smaller absolute value, regardless of sign.
     * Effectively truncates by discarding fractional parts. Positive numbers
     * round down, negative numbers round up (less negative).
     *
     * ```php
     * numerus(2.3)->roundTowardsZero();  // 2
     * numerus(2.7)->roundTowardsZero();  // 2
     * numerus(-2.3)->roundTowardsZero(); // -2
     * numerus(-2.7)->roundTowardsZero(); // -2
     * numerus(2.19)->roundTowardsZero(1); // 2.1
     * ```
     *
     * @param  int  $precision Number of decimal places to round to (default: 0)
     * @return self A new Numerus instance with the rounded value
     */
    public function roundTowardsZero(int $precision = 0): self
    {
        return new self($this->value->toScale($precision, BrickRoundingMode::DOWN));
    }

    /**
     * Round toward positive infinity at the specified precision (ceiling).
     *
     * Always rounds toward larger values on the number line. Positive numbers
     * round up, negative numbers round up (less negative). Equivalent to
     * ceiling operation when precision is 0.
     *
     * ```php
     * numerus(2.3)->roundPositiveInfinity();  // 3
     * numerus(2.7)->roundPositiveInfinity();  // 3
     * numerus(-2.3)->roundPositiveInfinity(); // -2
     * numerus(-2.7)->roundPositiveInfinity(); // -2
     * numerus(2.11)->roundPositiveInfinity(1); // 2.2
     * ```
     *
     * @param  int  $precision Number of decimal places to round to (default: 0)
     * @return self A new Numerus instance with the rounded value
     */
    public function roundPositiveInfinity(int $precision = 0): self
    {
        return new self($this->value->toScale($precision, BrickRoundingMode::CEILING));
    }

    /**
     * Round toward negative infinity at the specified precision (floor).
     *
     * Always rounds toward smaller values on the number line. Positive numbers
     * round down, negative numbers round down (more negative). Equivalent to
     * floor operation when precision is 0.
     *
     * ```php
     * numerus(2.3)->roundNegativeInfinity();  // 2
     * numerus(2.7)->roundNegativeInfinity();  // 2
     * numerus(-2.3)->roundNegativeInfinity(); // -3
     * numerus(-2.7)->roundNegativeInfinity(); // -3
     * numerus(2.19)->roundNegativeInfinity(1); // 2.1
     * ```
     *
     * @param  int  $precision Number of decimal places to round to (default: 0)
     * @return self A new Numerus instance with the rounded value
     */
    public function roundNegativeInfinity(int $precision = 0): self
    {
        return new self($this->value->toScale($precision, BrickRoundingMode::FLOOR));
    }

    /**
     * Round to nearest value, with ties resolved away from zero.
     *
     * Standard rounding behavior taught in schools. When exactly halfway
     * between two values, rounds to the value with larger absolute value.
     * This is the default rounding mode for round() method.
     *
     * ```php
     * numerus(2.5)->roundHalfAwayFromZero();  // 3 (tie rounds up)
     * numerus(2.4)->roundHalfAwayFromZero();  // 2
     * numerus(2.6)->roundHalfAwayFromZero();  // 3
     * numerus(-2.5)->roundHalfAwayFromZero(); // -3 (tie rounds away from zero)
     * numerus(2.25)->roundHalfAwayFromZero(1); // 2.3 (tie at 0.25 rounds away)
     * ```
     *
     * @param  int  $precision Number of decimal places to round to (default: 0)
     * @return self A new Numerus instance with the rounded value
     */
    public function roundHalfAwayFromZero(int $precision = 0): self
    {
        return new self($this->value->toScale($precision, BrickRoundingMode::HALF_UP));
    }

    /**
     * Round to nearest value, with ties resolved toward zero.
     *
     * When exactly halfway between two values, rounds to the value with
     * smaller absolute value. Opposite behavior of HalfAwayFromZero mode.
     *
     * ```php
     * numerus(2.5)->roundHalfTowardsZero();  // 2 (tie rounds down)
     * numerus(2.4)->roundHalfTowardsZero();  // 2
     * numerus(2.6)->roundHalfTowardsZero();  // 3
     * numerus(-2.5)->roundHalfTowardsZero(); // -2 (tie rounds toward zero)
     * numerus(2.25)->roundHalfTowardsZero(1); // 2.2 (tie at 0.25 rounds toward zero)
     * ```
     *
     * @param  int  $precision Number of decimal places to round to (default: 0)
     * @return self A new Numerus instance with the rounded value
     */
    public function roundHalfTowardsZero(int $precision = 0): self
    {
        return new self($this->value->toScale($precision, BrickRoundingMode::HALF_DOWN));
    }

    /**
     * Round to nearest value, with ties resolved to even numbers (banker's rounding).
     *
     * When exactly halfway between two values, rounds to the nearest even number.
     * Minimizes cumulative rounding bias in financial and statistical calculations
     * by distributing tie-breaks evenly between rounding up and down.
     *
     * ```php
     * numerus(2.5)->roundHalfEven();  // 2 (tie rounds to even)
     * numerus(3.5)->roundHalfEven();  // 4 (tie rounds to even)
     * numerus(2.4)->roundHalfEven();  // 2
     * numerus(2.6)->roundHalfEven();  // 3
     * numerus(-2.5)->roundHalfEven(); // -2 (tie rounds to even)
     * numerus(2.25)->roundHalfEven(1); // 2.2 (tie at 0.25 rounds to even 0.2)
     * ```
     *
     * @param  int  $precision Number of decimal places to round to (default: 0)
     * @return self A new Numerus instance with the rounded value
     */
    public function roundHalfEven(int $precision = 0): self
    {
        return new self($this->value->toScale($precision, BrickRoundingMode::HALF_EVEN));
    }

    /**
     * Round to nearest value, with ties resolved to odd numbers.
     *
     * When exactly halfway between two values, rounds to the nearest odd number.
     * Opposite of HalfEven (banker's rounding), providing an alternative
     * tie-breaking strategy for specialized use cases.
     *
     * ```php
     * numerus(2.5)->roundHalfOdd();  // 3 (tie rounds to odd)
     * numerus(3.5)->roundHalfOdd();  // 3 (tie rounds to odd)
     * numerus(2.4)->roundHalfOdd();  // 2
     * numerus(2.6)->roundHalfOdd();  // 3
     * numerus(-2.5)->roundHalfOdd(); // -3 (tie rounds to odd)
     * numerus(2.25)->roundHalfOdd(1); // 2.3 (tie at 0.25 rounds to odd 0.3)
     * ```
     *
     * @param  int  $precision Number of decimal places to round to (default: 0)
     * @return self A new Numerus instance with the rounded value
     */
    public function roundHalfOdd(int $precision = 0): self
    {
        return $this->round($precision, RoundingMode::HalfOdd);
    }

    /**
     * Negate this number (multiply by -1).
     *
     * @return self A new Numerus instance with the negated value
     */
    public function negate(): self
    {
        return new self($this->value->negated());
    }

    /**
     * Raise this number to a power.
     *
     * @param  float|int $exponent The exponent to raise the number to
     * @return self      A new Numerus instance with the result of the exponentiation
     */
    public function power(int|float $exponent): self
    {
        // For integer exponents, use BigDecimal's power method
        if (is_int($exponent) && $exponent >= 0) {
            return new self($this->value->power($exponent));
        }

        // For negative or float exponents, convert to native, calculate, then convert back
        $result = $this->value->toFloat() ** $exponent;

        return new self(BigDecimal::of($result));
    }

    /**
     * Calculate the square root of this number.
     *
     * @throws InvalidArgumentException When attempting to calculate square root of a negative number
     *
     * @return self A new Numerus instance with the square root value
     */
    public function sqrt(): self
    {
        throw_if($this->value->isNegative(), InvalidArgumentException::class, 'Cannot calculate square root of negative number');

        // BigDecimal doesn't have sqrt, so we use native sqrt
        $result = sqrt($this->value->toFloat());

        return new self(BigDecimal::of($result));
    }

    /**
     * Calculate the modulo (remainder) of dividing this number by a divisor.
     *
     * @param float|int|self $divisor The divisor for the modulo operation
     *
     * @throws InvalidArgumentException When attempting modulo by zero
     *
     * @return self A new Numerus instance with the remainder value
     */
    public function mod(int|float|self $divisor): self
    {
        $value = $divisor instanceof self ? $divisor->value : BigDecimal::of($divisor);

        throw_if($value->isZero(), InvalidArgumentException::class, 'Modulo by zero');

        return new self($this->value->remainder($value));
    }

    /**
     * Check if this number is positive (greater than zero).
     *
     * @return bool True if the value is greater than zero
     */
    public function isPositive(): bool
    {
        return $this->value->isPositive();
    }

    /**
     * Check if this number is negative (less than zero).
     *
     * @return bool True if the value is less than zero
     */
    public function isNegative(): bool
    {
        return $this->value->isNegative();
    }

    /**
     * Check if this number equals zero.
     *
     * @return bool True if the value equals zero (handles both int and float zero)
     */
    public function isZero(): bool
    {
        return $this->value->isZero();
    }

    /**
     * Check if this number is even.
     *
     * @return bool True if the value is an integer and divisible by 2
     */
    public function isEven(): bool
    {
        // Check if it's an integer value first
        if ($this->value->getScale() > 0) {
            return false;
        }

        // Check if divisible by 2
        return $this->value->remainder(BigDecimal::of(2))->isZero();
    }

    /**
     * Check if this number is odd.
     *
     * @return bool True if the value is an integer and not divisible by 2
     */
    public function isOdd(): bool
    {
        // Check if it's an integer value first
        if ($this->value->getScale() > 0) {
            return false;
        }

        // Check if not divisible by 2
        return !$this->value->remainder(BigDecimal::of(2))->isZero();
    }

    /**
     * Check if this number represents an integer value.
     *
     * Returns true for actual integers or floats with no fractional component.
     *
     * @return bool True if the value has no decimal places
     */
    public function isInteger(): bool
    {
        if ($this->value->getScale() === 0) {
            return true;
        }

        return $this->value->stripTrailingZeros()->getScale() === 0;
    }

    /**
     * Check if this number represents a floating-point value.
     *
     * @return bool True if the value has a fractional component
     */
    public function isFloat(): bool
    {
        return !$this->isInteger();
    }

    /**
     * Get the sign of this number.
     *
     * @return int Returns 1 for positive, -1 for negative, or 0 for zero
     */
    public function sign(): int
    {
        return $this->value->getSign();
    }

    /**
     * Check if this number equals another value.
     *
     * @param  float|int|self $other The value to compare against
     * @return bool           True if the values are exactly equal
     */
    public function equals(int|float|self $other): bool
    {
        $value = $other instanceof self ? $other->value : BigDecimal::of($other);

        return $this->value->compareTo($value) === 0;
    }

    /**
     * Check if this number does not equal another value.
     *
     * @param  float|int|self $other The value to compare against
     * @return bool           True if the values are not equal
     */
    public function notEquals(int|float|self $other): bool
    {
        return !$this->equals($other);
    }

    /**
     * Check if this number is greater than another value.
     *
     * @param  float|int|self $other The value to compare against
     * @return bool           True if this value is greater
     */
    public function greaterThan(int|float|self $other): bool
    {
        $value = $other instanceof self ? $other->value : BigDecimal::of($other);

        return $this->value->compareTo($value) > 0;
    }

    /**
     * Check if this number is greater than or equal to another value.
     *
     * @param  float|int|self $other The value to compare against
     * @return bool           True if this value is greater than or equal
     */
    public function greaterThanOrEqual(int|float|self $other): bool
    {
        $value = $other instanceof self ? $other->value : BigDecimal::of($other);

        return $this->value->compareTo($value) >= 0;
    }

    /**
     * Check if this number is less than another value.
     *
     * @param  float|int|self $other The value to compare against
     * @return bool           True if this value is less
     */
    public function lessThan(int|float|self $other): bool
    {
        $value = $other instanceof self ? $other->value : BigDecimal::of($other);

        return $this->value->compareTo($value) < 0;
    }

    /**
     * Check if this number is less than or equal to another value.
     *
     * @param  float|int|self $other The value to compare against
     * @return bool           True if this value is less than or equal
     */
    public function lessThanOrEqual(int|float|self $other): bool
    {
        $value = $other instanceof self ? $other->value : BigDecimal::of($other);

        return $this->value->compareTo($value) <= 0;
    }

    /**
     * Check if this number falls between two values.
     *
     * @param  float|int|self $min       The minimum boundary value
     * @param  float|int|self $max       The maximum boundary value
     * @param  bool           $inclusive Whether to include the boundaries (default: true)
     * @return bool           True if this value is within the specified range
     */
    public function between(int|float|self $min, int|float|self $max, bool $inclusive = true): bool
    {
        $minValue = $min instanceof self ? $min->value : BigDecimal::of($min);
        $maxValue = $max instanceof self ? $max->value : BigDecimal::of($max);

        if ($inclusive) {
            return $this->value->compareTo($minValue) >= 0 && $this->value->compareTo($maxValue) <= 0;
        }

        return $this->value->compareTo($minValue) > 0 && $this->value->compareTo($maxValue) < 0;
    }

    /**
     * Check if this number falls outside a range.
     *
     * @param  float|int|self $min       The minimum boundary value
     * @param  float|int|self $max       The maximum boundary value
     * @param  bool           $inclusive Whether boundaries are considered part of the range (default: true)
     * @return bool           True if this value is outside the specified range
     */
    public function notBetween(int|float|self $min, int|float|self $max, bool $inclusive = true): bool
    {
        return !$this->between($min, $max, $inclusive);
    }

    /**
     * Get the minimum of this number and another value.
     *
     * @param  float|int|self $other The value to compare against
     * @return self           A new Numerus instance with the smaller value
     */
    public function min(int|float|self $other): self
    {
        $value = $other instanceof self ? $other->value : BigDecimal::of($other);

        return new self($this->value->compareTo($value) < 0 ? $this->value : $value);
    }

    /**
     * Get the maximum of this number and another value.
     *
     * @param  float|int|self $other The value to compare against
     * @return self           A new Numerus instance with the larger value
     */
    public function max(int|float|self $other): self
    {
        $value = $other instanceof self ? $other->value : BigDecimal::of($other);

        return new self($this->value->compareTo($value) > 0 ? $this->value : $value);
    }

    /**
     * Clamp the number between a minimum and maximum value.
     *
     * Restricts the value to fall within a specified range. If the value is
     * below the minimum, returns the minimum. If above the maximum, returns
     * the maximum. Otherwise, returns the original value.
     *
     * @param float|int|self $min The lower boundary for clamping
     * @param float|int|self $max The upper boundary for clamping
     *
     * @throws InvalidArgumentException When min is greater than max
     *
     * @return self A new Numerus instance with the clamped value
     *
     * @see https://laravel.com/docs/12.x/helpers#method-number-clamp
     */
    public function clamp(int|float|self $min, int|float|self $max): self
    {
        $minValue = $min instanceof self ? $min->value : BigDecimal::of($min);
        $maxValue = $max instanceof self ? $max->value : BigDecimal::of($max);

        throw_if($minValue->compareTo($maxValue) > 0, InvalidArgumentException::class, 'Min value cannot be greater than max value');

        // Clamp: max(min, min(max, value))
        $temp = $this->value->compareTo($maxValue) > 0 ? $maxValue : $this->value;
        $result = $temp->compareTo($minValue) < 0 ? $minValue : $temp;

        return new self($result);
    }

    /**
     * Calculate what percentage this value is of a given total.
     *
     * ```php
     * numerus(25)->percentOf(100); // 25.0 (25 is 25% of 100)
     * numerus(50)->percentOf(200); // 25.0 (50 is 25% of 200)
     * ```
     *
     * @param float|int|self $total The total value to calculate the percentage against
     *
     * @throws InvalidArgumentException When total is zero
     *
     * @return float The percentage this value represents of the total
     */
    public function percentOf(int|float|self $total): float
    {
        $totalValue = $total instanceof self ? $total->value : BigDecimal::of($total);

        throw_if($totalValue->isZero(), InvalidArgumentException::class, 'Cannot calculate percentage of zero');

        return $this->value->dividedBy($totalValue, 10, BrickRoundingMode::HALF_UP)->multipliedBy(100)->toFloat();
    }

    /**
     * Add a percentage to this value.
     *
     * ```php
     * numerus(100)->addPercent(20); // 120 (100 + 20% of 100)
     * numerus(50)->addPercent(10);  // 55 (50 + 10% of 50)
     * ```
     *
     * @param  float|int $percent The percentage to add (e.g., 20 for 20%)
     * @return self      A new Numerus instance with the percentage added
     */
    public function addPercent(int|float $percent): self
    {
        $percentValue = BigDecimal::of($percent)->dividedBy(100, 10, BrickRoundingMode::HALF_UP);
        $increase = $this->value->multipliedBy($percentValue);

        return new self($this->value->plus($increase));
    }

    /**
     * Subtract a percentage from this value.
     *
     * ```php
     * numerus(100)->subtractPercent(20); // 80 (100 - 20% of 100)
     * numerus(50)->subtractPercent(10);  // 45 (50 - 10% of 50)
     * ```
     *
     * @param  float|int $percent The percentage to subtract (e.g., 20 for 20%)
     * @return self      A new Numerus instance with the percentage subtracted
     */
    public function subtractPercent(int|float $percent): self
    {
        $percentValue = BigDecimal::of($percent)->dividedBy(100, 10, BrickRoundingMode::HALF_UP);
        $decrease = $this->value->multipliedBy($percentValue);

        return new self($this->value->minus($decrease));
    }

    /**
     * Calculate the percentage change from this value to another value.
     *
     * Returns positive percentages for increases and negative for decreases.
     *
     * ```php
     * numerus(50)->percentageChange(75);  // 50.0 (50% increase)
     * numerus(100)->percentageChange(80); // -20.0 (20% decrease)
     * ```
     *
     * @param float|int|self $newValue The new value to compare against
     *
     * @throws InvalidArgumentException When attempting to calculate from zero
     *
     * @return float The percentage change (positive for increase, negative for decrease)
     */
    public function percentageChange(int|float|self $newValue): float
    {
        $new = $newValue instanceof self ? $newValue->value : BigDecimal::of($newValue);

        throw_if($this->value->isZero(), InvalidArgumentException::class, 'Cannot calculate percentage change from zero');

        return $new->minus($this->value)
            ->dividedBy($this->value, 10, BrickRoundingMode::HALF_UP)
            ->multipliedBy(100)
            ->toFloat();
    }

    /**
     * Calculate the greatest common divisor (GCD) of this value and another.
     *
     * Uses Euclidean algorithm to find the largest positive integer that
     * divides both numbers without a remainder.
     *
     * @param  int|self $other The value to calculate GCD with
     * @return self     A new Numerus instance with the GCD value
     */
    public function gcd(int|self $other): self
    {
        $a = abs($this->value->toInt());
        $b = abs($other instanceof self ? $other->value->toInt() : $other);

        while ($b !== 0) {
            $temp = $b;
            $b = $a % $b;
            $a = $temp;
        }

        return new self(BigDecimal::of($a));
    }

    /**
     * Calculate the least common multiple (LCM) of this value and another.
     *
     * Finds the smallest positive integer that is divisible by both numbers.
     * Useful for fraction operations and cycle calculations.
     *
     * @param int|self $other The value to calculate LCM with
     *
     * @throws InvalidArgumentException When either value is zero
     *
     * @return self A new Numerus instance with the LCM value
     */
    public function lcm(int|self $other): self
    {
        $otherValue = $other instanceof self ? $other->value->toInt() : $other;

        throw_if($this->value->isZero() || $otherValue === 0, InvalidArgumentException::class, 'Cannot calculate LCM with zero');

        $gcd = $this->gcd($other)->value();

        return new self(BigDecimal::of(abs(($this->value->toInt() * $otherValue) / $gcd)));
    }

    /**
     * Calculate the factorial of this value.
     *
     * Computes n! = n × (n-1) × (n-2) × ... × 2 × 1.
     * Only works with non-negative integers due to mathematical constraints.
     *
     * @throws InvalidArgumentException When value is negative or not an integer
     *
     * @return self A new Numerus instance with the factorial value
     */
    public function factorial(): self
    {
        throw_if($this->value->isNegative(), InvalidArgumentException::class, 'Cannot calculate factorial of negative number');
        throw_if(!$this->isInteger(), InvalidArgumentException::class, 'Factorial requires an integer value');

        $intValue = $this->value->toInt();

        if ($intValue === 0 || $intValue === 1) {
            return new self(BigDecimal::of(1));
        }

        $result = 1;

        for ($i = 2; $i <= $intValue; ++$i) {
            $result *= $i;
        }

        return new self(BigDecimal::of($result));
    }

    /**
     * Convert to integer, truncating any decimal places.
     *
     * Converts the value to an integer by truncating (not rounding) the
     * decimal portion. Use round()->toInt() if you need rounding behavior.
     *
     * @return int The value cast to an integer
     *
     * @see integerPart() For extracting just the integer portion
     */
    public function toInt(): int
    {
        return $this->value->dividedBy(1, 0, BrickRoundingMode::DOWN)->toBigInteger()->toInt();
    }

    /**
     * Convert to floating-point number.
     *
     * Returns the value as a native PHP float. Note that this may lose
     * precision for very large numbers or numbers with many decimal places.
     *
     * @return float The value cast to a float
     */
    public function toFloat(): float
    {
        return $this->value->toFloat();
    }

    /**
     * Convert to string representation.
     *
     * Returns the exact numeric value as a string without any formatting.
     * For locale-aware formatting, use format() or other formatting methods.
     *
     * @return string The value as a string
     *
     * @see format() For locale-aware number formatting
     */
    public function toString(): string
    {
        return $this->value->__toString();
    }

    // Laravel Number formatting methods

    /**
     * Format the number with abbreviated unit suffixes (K, M, B, T).
     *
     * Converts large numbers into compact notation using standard metric
     * prefixes for improved readability in UI contexts.
     *
     * @param  int    $precision Number of decimal places to display (default: 0)
     * @return string The abbreviated number (e.g., "1K", "2.5M", "1B")
     *
     * @see https://laravel.com/docs/12.x/helpers#method-number-abbreviate
     */
    public function abbreviate(int $precision = 0): string
    {
        /** @var string */
        return Number::abbreviate($this->toNative($this->value), $precision);
    }

    /**
     * Convert the number into a human-readable string.
     *
     * Transforms numbers into natural language representations like
     * "1 thousand", "2.5 million" for better comprehension in prose.
     *
     * @param  int         $precision Number of decimal places to display (default: 0)
     * @param  null|string $locale    Optional locale for localized number words
     * @return string      The human-readable number representation
     *
     * @see https://laravel.com/docs/12.x/helpers#method-number-for-humans
     */
    public function forHumans(int $precision = 0, ?string $locale = null): string
    {
        /** @var string */
        return Number::forHumans($this->toNative($this->value), $precision);
    }

    /**
     * Format bytes as human-readable file size.
     *
     * Converts byte values into appropriate units (KB, MB, GB, TB)
     * for displaying file or memory sizes in user interfaces.
     *
     * @param  int    $precision Number of decimal places to display (default: 0)
     * @return string The formatted file size (e.g., "1 KB", "2.5 MB", "1 GB")
     *
     * @see https://laravel.com/docs/12.x/helpers#method-number-file-size
     */
    public function fileSize(int $precision = 0): string
    {
        return Number::fileSize($this->toNative($this->value), $precision);
    }

    /**
     * Format the number with locale-aware thousands separators and decimal points.
     *
     * @param  int         $precision    Minimum number of decimal places to display (default: 0)
     * @param  null|int    $maxPrecision Maximum decimal places; allows trailing zeros to be trimmed
     * @param  null|string $locale       Optional locale for culture-specific formatting
     * @return string      The formatted number (e.g., "1,234.56" in en_US, "1.234,56" in de_DE)
     *
     * @see https://laravel.com/docs/12.x/helpers#method-number-format
     */
    public function format(int $precision = 0, ?int $maxPrecision = null, ?string $locale = null): string
    {
        /** @var string */
        return Number::format($this->toNative($this->value), $precision, $maxPrecision, $locale);
    }

    /**
     * Format the number as currency with locale-aware symbols and formatting.
     *
     * @param  string      $in        ISO 4217 currency code (default: 'USD')
     * @param  null|string $locale    Optional locale for culture-specific formatting
     * @param  int         $precision Number of decimal places to display (default: 2)
     * @return string      The formatted currency (e.g., "$1,234.56", "€1.234,56")
     *
     * @see https://laravel.com/docs/12.x/helpers#method-number-currency
     */
    public function currency(string $in = 'USD', ?string $locale = null, int $precision = 2): string
    {
        /** @var string */
        return Number::currency($this->toNative($this->value), $in, $locale);
    }

    /**
     * Format the number as a percentage with locale-aware formatting.
     *
     * @param  int         $precision    Minimum number of decimal places to display (default: 0)
     * @param  null|int    $maxPrecision Maximum decimal places; allows trailing zeros to be trimmed
     * @param  null|string $locale       Optional locale for culture-specific formatting
     * @return string      The formatted percentage (e.g., "50%", "33.33%")
     *
     * @see https://laravel.com/docs/12.x/helpers#method-number-percentage
     */
    public function percentage(int $precision = 0, ?int $maxPrecision = null, ?string $locale = null): string
    {
        /** @var string */
        return Number::percentage($this->toNative($this->value), $precision, $maxPrecision, $locale);
    }

    /**
     * Format the number as an ordinal.
     *
     * Converts numeric values into ordinal notation for representing
     * position in a sequence.
     *
     * @param  null|string $locale Optional locale for language-specific ordinal formatting
     * @return string      The ordinal representation (e.g., "1st", "2nd", "3rd", "21st")
     *
     * @see https://laravel.com/docs/12.x/helpers#method-number-ordinal
     */
    public function ordinal(?string $locale = null): string
    {
        return Number::ordinal($this->toNative($this->value), $locale);
    }

    /**
     * Spell out the number as words.
     *
     * Converts numeric values into their written form for formal documents,
     * checks, or accessibility purposes.
     *
     * @param  null|string $locale Optional locale for language-specific word spellings
     * @return string      The number spelled out (e.g., "forty-two", "one hundred")
     *
     * @see https://laravel.com/docs/12.x/helpers#method-number-spell
     */
    public function spell(?string $locale = null): string
    {
        return Number::spell($this->toNative($this->value), $locale);
    }

    /**
     * Spell out the number as ordinal words.
     *
     * Converts numeric values into written ordinal form for formal contexts
     * or enhanced readability.
     *
     * @param  null|string $locale Optional locale for language-specific ordinal words
     * @return string      The ordinal spelled out (e.g., "first", "second", "forty-second")
     *
     * @see https://laravel.com/docs/12.x/helpers#method-number-spell-ordinal
     */
    public function spellOrdinal(?string $locale = null): string
    {
        return Number::spellOrdinal($this->toNative($this->value), $locale);
    }

    /**
     * Remove trailing zeros from a decimal number.
     *
     * Cleans up decimal representations by removing unnecessary trailing
     * zeros, making numbers more concise (e.g., 1.500 becomes 1.5).
     *
     * @return self A new Numerus instance with trailing zeros removed
     *
     * @see https://laravel.com/docs/12.x/helpers#method-number-trim
     */
    public function trim(): self
    {
        // If it's an integer (no decimal part), return as is
        if ($this->isInteger()) {
            return $this;
        }

        // Use BigDecimal's stripTrailingZeros to remove trailing zeros
        return new self($this->value->stripTrailingZeros());
    }

    /**
     * Convert BigDecimal to native int or float.
     *
     * Returns int if no fractional part exists and value can fit in int range,
     * otherwise returns float. This method is used internally for interoperability
     * with Laravel's Number helper and other native PHP functions.
     *
     * @param  BigDecimal $decimal The BigDecimal value to convert
     * @return float|int  The converted native numeric value
     */
    private function toNative(BigDecimal $decimal): int|float
    {
        // Strip trailing zeros to check actual fractional content
        $stripped = $decimal->stripTrailingZeros();

        // If has fractional part or value is too large for int, return float
        if ($stripped->getScale() > 0) {
            return $decimal->toFloat();
        }

        // Return as int if it fits, otherwise float
        try {
            return $stripped->toInt();
        } catch (IntegerOverflowException) {
            return $decimal->toFloat();
        }
    }

    /**
     * Convert PHP RoundingMode enum to BrickRoundingMode.
     *
     * Maps PHP's native RoundingMode enum (PHP 8.3+) to the corresponding
     * Brick\Math rounding mode constant. This enables use of PHP's standard
     * rounding modes while leveraging Brick\Math's arbitrary-precision arithmetic.
     *
     * @param  RoundingMode      $mode The PHP RoundingMode enum value
     * @return BrickRoundingMode The corresponding BrickRoundingMode
     */
    private function toBrickRoundingMode(RoundingMode $mode): BrickRoundingMode
    {
        return match ($mode) {
            RoundingMode::HalfAwayFromZero => BrickRoundingMode::HALF_UP,
            RoundingMode::HalfTowardsZero => BrickRoundingMode::HALF_DOWN,
            RoundingMode::HalfEven => BrickRoundingMode::HALF_EVEN,
            RoundingMode::HalfOdd => BrickRoundingMode::HALF_CEILING,
            RoundingMode::TowardsZero => BrickRoundingMode::DOWN,
            RoundingMode::AwayFromZero => BrickRoundingMode::UP,
            RoundingMode::NegativeInfinity => BrickRoundingMode::FLOOR,
            RoundingMode::PositiveInfinity => BrickRoundingMode::CEILING,
        };
    }
}
