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
 * Exception thrown when division by zero is attempted.
 *
 * Raised during arithmetic division operations where the divisor evaluates to zero,
 * producing a mathematically undefined result.
 *
 * @author Brian Faust <brian@cline.sh>
 *
 * @since 1.0.0
 */
final class DivisionByZeroException extends InvalidArgumentException implements NumerusException
{
    /**
     * Create a division by zero exception.
     *
     * @return self The exception instance with a descriptive message
     */
    public static function create(): self
    {
        return new self('Division by zero');
    }
}
