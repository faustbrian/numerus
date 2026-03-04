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
 * Exception thrown when percentage change calculation cannot be performed.
 *
 * Raised when the original (baseline) value is zero, making the relative percentage
 * change formula (new - original) / original undefined due to division by zero.
 *
 * @author Brian Faust <brian@cline.sh>
 *
 * @since 1.0.0
 */
final class CannotCalculatePercentageChangeException extends InvalidArgumentException implements NumerusException
{
    /**
     * Create an exception for a percentage change calculation attempted from a zero baseline.
     *
     * @return self The exception instance with a descriptive message
     */
    public static function fromZero(): self
    {
        return new self('Cannot calculate percentage change from zero');
    }
}
