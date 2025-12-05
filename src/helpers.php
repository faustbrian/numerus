<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\Numerus;

/**
 * Create a new Numerus instance.
 *
 * Convenience helper function providing a fluent entry point for creating
 * immutable numeric value objects. This helper enables clean, readable code
 * for mathematical operations and formatting chains.
 *
 * ```php
 * // Basic usage
 * $num = numerus(100);
 *
 * // Fluent chaining
 * $result = numerus(100)
 *     ->addPercent(20)
 *     ->divideBy(2)
 *     ->format(2);
 *
 * // With calculations
 * $average = numerus(Numerus::average([10, 20, 30]))
 *     ->round(2)
 *     ->toString();
 * ```
 *
 * @see Numerus::create() The underlying factory method called by this helper
 * @param  float|int $value The numeric value to wrap in a Numerus instance.
 *                          Accepts both integers and floating-point numbers
 *                          for maximum flexibility in numeric operations.
 * @return Numerus   An immutable Numerus value object providing access to
 *                   mathematical operations, comparisons, and locale-aware
 *                   formatting methods
 * @since 1.0.0
 */
function numerus(int|float $value): Numerus
{
    return Numerus::create($value);
}
