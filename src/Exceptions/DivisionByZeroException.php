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
 * @author Brian Faust <brian@cline.sh>
 */
final class DivisionByZeroException extends InvalidArgumentException implements NumerusException
{
    public static function create(): self
    {
        return new self('Division by zero');
    }
}
