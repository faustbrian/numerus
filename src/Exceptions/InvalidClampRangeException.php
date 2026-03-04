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
 * Exception thrown when an invalid clamp range is provided.
 *
 * Raised by Numerus::clamp() when the minimum boundary exceeds the maximum,
 * which represents a logically impossible range constraint.
 *
 * @author Brian Faust <brian@cline.sh>
 *
 * @since 1.0.0
 */
final class InvalidClampRangeException extends InvalidArgumentException implements NumerusException
{
    /**
     * Create an exception for when the minimum value exceeds the maximum.
     *
     * @return self The exception instance with a descriptive message
     */
    public static function minGreaterThanMax(): self
    {
        return new self('Min value cannot be greater than max value');
    }
}
