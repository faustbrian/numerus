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
 * Exception thrown when average calculation cannot be performed.
 *
 * Raised when attempting to compute the average of an empty set of values,
 * where the operation is mathematically undefined.
 *
 * @author Brian Faust <brian@cline.sh>
 *
 * @since 1.0.0
 */
final class CannotCalculateAverageException extends InvalidArgumentException implements NumerusException
{
    /**
     * Create an exception for an average calculation attempted on an empty array.
     *
     * @return self The exception instance with a descriptive message
     */
    public static function emptyArray(): self
    {
        return new self('Cannot calculate average of empty array');
    }
}
