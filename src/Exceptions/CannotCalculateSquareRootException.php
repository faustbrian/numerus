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
 * Exception thrown when square root of a negative number is attempted.
 *
 * Raised when attempting to compute the square root of a negative value, which
 * produces a complex (imaginary) number and is not supported in real-number arithmetic.
 *
 * @author Brian Faust <brian@cline.sh>
 *
 * @since 1.0.0
 */
final class CannotCalculateSquareRootException extends InvalidArgumentException implements NumerusException
{
    /**
     * Create an exception for a square root calculation attempted on a negative number.
     *
     * @return self The exception instance with a descriptive message
     */
    public static function negativeNumber(): self
    {
        return new self('Cannot calculate square root of negative number');
    }
}
