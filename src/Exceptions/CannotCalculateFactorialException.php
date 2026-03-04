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
 * Exception thrown when factorial calculation cannot be performed on a negative number.
 *
 * Raised when attempting to compute the factorial of a negative integer, which is
 * mathematically undefined in the standard (non-gamma-extended) definition.
 *
 * @author Brian Faust <brian@cline.sh>
 *
 * @since 1.0.0
 */
final class CannotCalculateFactorialException extends InvalidArgumentException implements NumerusException
{
    /**
     * Create an exception for a factorial calculation attempted on a negative number.
     *
     * @return self The exception instance with a descriptive message
     */
    public static function negativeNumber(): self
    {
        return new self('Cannot calculate factorial of negative number');
    }
}
