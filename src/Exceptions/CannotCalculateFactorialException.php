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
 * @author Brian Faust <brian@cline.sh>
 */
final class CannotCalculateFactorialException extends InvalidArgumentException implements NumerusException
{
    public static function negativeNumber(): self
    {
        return new self('Cannot calculate factorial of negative number');
    }
}
