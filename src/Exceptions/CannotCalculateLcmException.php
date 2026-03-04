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
 * Exception thrown when LCM calculation cannot be performed with zero.
 *
 * Raised when zero is supplied as one of the operands for a least common multiple
 * calculation. The LCM of any number with zero is undefined, as it would require
 * division by zero during the greatest common divisor step.
 *
 * @author Brian Faust <brian@cline.sh>
 *
 * @since 1.0.0
 */
final class CannotCalculateLcmException extends InvalidArgumentException implements NumerusException
{
    /**
     * Create an exception for an LCM calculation attempted with a zero operand.
     *
     * @return self The exception instance with a descriptive message
     */
    public static function withZero(): self
    {
        return new self('Cannot calculate LCM with zero');
    }
}
