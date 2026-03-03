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
 * Exception thrown when a modulo-by-zero operation is attempted.
 *
 * Raised by Numerus::mod() when the divisor is zero, which has no
 * defined remainder in mathematics.
 *
 * @author Brian Faust <brian@cline.sh>
 *
 * @since 1.0.0
 */
final class ModuloByZeroException extends InvalidArgumentException implements NumerusException
{
    /**
     * Create a modulo-by-zero exception.
     *
     * @return self The exception instance with a descriptive message
     */
    public static function create(): self
    {
        return new self('Modulo by zero');
    }
}
