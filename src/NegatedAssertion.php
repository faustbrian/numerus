<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\Numerus;

/**
 * Proxy for negated assertions on Numerus instances.
 *
 * Provides an inverted fluent API for comparison operations, allowing
 * intuitive negated assertions via the not() method on Numerus.
 *
 * ```php
 * numerus(10)->not()->equals(5);              // true (10 != 5)
 * numerus(10)->not()->greaterThan(20);        // true (10 <= 20)
 * numerus(10)->not()->between(5, 15);         // false (10 is between 5 and 15)
 * ```
 *
 * @author Brian Faust <brian@cline.sh>
 *
 * @since 1.0.0
 *
 * @psalm-immutable
 */
final readonly class NegatedAssertion
{
    /**
     * Create a new negated assertion proxy.
     *
     * @param Numerus $numerus The Numerus instance to wrap with negated assertions
     */
    public function __construct(
        private Numerus $numerus,
    ) {}

    /**
     * Check if this number does not equal another value.
     *
     * Inverted version of equals(). Returns true when values differ.
     *
     * ```php
     * numerus(10)->not()->equals(5);   // true (10 != 5)
     * numerus(10)->not()->equals(10);  // false (10 == 10)
     * ```
     *
     * @param  float|int|Numerus $other The value to compare against
     * @return bool              True if the values are not equal
     */
    public function equals(int|float|Numerus $other): bool
    {
        return !$this->numerus->equals($other);
    }

    /**
     * Check if this number equals another value.
     *
     * Inverted version of notEquals(). Returns true when values are the same.
     *
     * ```php
     * numerus(10)->not()->notEquals(10);  // true (10 == 10)
     * numerus(10)->not()->notEquals(5);   // false (10 != 5)
     * ```
     *
     * @param  float|int|Numerus $other The value to compare against
     * @return bool              True if the values are equal
     */
    public function notEquals(int|float|Numerus $other): bool
    {
        return !$this->numerus->notEquals($other);
    }

    /**
     * Check if this number is less than or equal to another value.
     *
     * Inverted version of greaterThan(). Returns true when this value
     * is not greater (i.e., less than or equal).
     *
     * ```php
     * numerus(10)->not()->greaterThan(20);  // true (10 <= 20)
     * numerus(10)->not()->greaterThan(5);   // false (10 > 5)
     * ```
     *
     * @param  float|int|Numerus $other The value to compare against
     * @return bool              True if this value is less than or equal
     */
    public function greaterThan(int|float|Numerus $other): bool
    {
        return !$this->numerus->greaterThan($other);
    }

    /**
     * Check if this number is less than another value.
     *
     * Inverted version of greaterThanOrEqual(). Returns true when this
     * value is strictly less than the other.
     *
     * ```php
     * numerus(10)->not()->greaterThanOrEqual(20);  // true (10 < 20)
     * numerus(10)->not()->greaterThanOrEqual(10);  // false (10 >= 10)
     * ```
     *
     * @param  float|int|Numerus $other The value to compare against
     * @return bool              True if this value is less than
     */
    public function greaterThanOrEqual(int|float|Numerus $other): bool
    {
        return !$this->numerus->greaterThanOrEqual($other);
    }

    /**
     * Check if this number is greater than or equal to another value.
     *
     * Inverted version of lessThan(). Returns true when this value
     * is not less (i.e., greater than or equal).
     *
     * ```php
     * numerus(10)->not()->lessThan(5);   // true (10 >= 5)
     * numerus(10)->not()->lessThan(20);  // false (10 < 20)
     * ```
     *
     * @param  float|int|Numerus $other The value to compare against
     * @return bool              True if this value is greater than or equal
     */
    public function lessThan(int|float|Numerus $other): bool
    {
        return !$this->numerus->lessThan($other);
    }

    /**
     * Check if this number is greater than another value.
     *
     * Inverted version of lessThanOrEqual(). Returns true when this
     * value is strictly greater than the other.
     *
     * ```php
     * numerus(10)->not()->lessThanOrEqual(5);   // true (10 > 5)
     * numerus(10)->not()->lessThanOrEqual(10);  // false (10 <= 10)
     * ```
     *
     * @param  float|int|Numerus $other The value to compare against
     * @return bool              True if this value is greater than
     */
    public function lessThanOrEqual(int|float|Numerus $other): bool
    {
        return !$this->numerus->lessThanOrEqual($other);
    }

    /**
     * Check if this number falls outside a range.
     *
     * Inverted version of between(). Returns true when this value
     * is not within the specified range.
     *
     * ```php
     * numerus(10)->not()->between(5, 15);          // false (10 is within range)
     * numerus(10)->not()->between(20, 30);         // true (10 is outside range)
     * numerus(10)->not()->between(10, 20, false);  // true (exclusive boundaries)
     * ```
     *
     * @param  float|int|Numerus $min       The minimum boundary value
     * @param  float|int|Numerus $max       The maximum boundary value
     * @param  bool              $inclusive Whether to include the boundaries (default: true)
     * @return bool              True if this value is outside the specified range
     */
    public function between(int|float|Numerus $min, int|float|Numerus $max, bool $inclusive = true): bool
    {
        return !$this->numerus->between($min, $max, $inclusive);
    }

    /**
     * Check if this number falls within a range.
     *
     * Inverted version of notBetween(). Returns true when this value
     * is within the specified range.
     *
     * ```php
     * numerus(10)->not()->notBetween(5, 15);         // true (10 is within range)
     * numerus(10)->not()->notBetween(20, 30);        // false (10 is outside range)
     * numerus(10)->not()->notBetween(10, 20, false); // false (exclusive boundaries)
     * ```
     *
     * @param  float|int|Numerus $min       The minimum boundary value
     * @param  float|int|Numerus $max       The maximum boundary value
     * @param  bool              $inclusive Whether boundaries are considered part of the range (default: true)
     * @return bool              True if this value is within the specified range
     */
    public function notBetween(int|float|Numerus $min, int|float|Numerus $max, bool $inclusive = true): bool
    {
        return !$this->numerus->notBetween($min, $max, $inclusive);
    }
}
