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
 * @author Brian Faust <brian@cline.sh>
 */
final class CannotCalculateSquareRootException extends InvalidArgumentException implements NumerusException
{
    public static function negativeNumber(): self
    {
        return new self('Cannot calculate square root of negative number');
    }
}
