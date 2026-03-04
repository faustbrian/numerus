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
 * Exception thrown when percentage calculation cannot be performed.
 *
 * Raised when the total (denominator) is zero, making the percentage formula
 * part / total * 100 undefined due to division by zero.
 *
 * @author Brian Faust <brian@cline.sh>
 *
 * @since 1.0.0
 */
final class CannotCalculatePercentageException extends InvalidArgumentException implements NumerusException
{
    /**
     * Create an exception for a percentage calculation attempted against a zero total.
     *
     * @return self The exception instance with a descriptive message
     */
    public static function zeroTotal(): self
    {
        return new self('Cannot calculate percentage of zero');
    }
}
