<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\Numerus\Exceptions;

use InvalidArgumentException;

/**
 * Exception thrown when factorial is attempted on a non-integer value.
 *
 * Raised when a floating-point number is passed to a factorial operation.
 * The standard factorial function is defined only for non-negative integers;
 * fractional inputs require the Gamma function instead.
 *
 * @author Brian Faust <brian@cline.sh>
 *
 * @since 1.0.0
 */
final class FactorialRequiresIntegerException extends InvalidArgumentException implements NumerusException
{
    /**
     * Create an exception for a factorial calculation attempted on a non-integer value.
     *
     * @return self The exception instance with a descriptive message
     */
    public static function create(): self
    {
        return new self('Factorial requires an integer value');
    }
}
