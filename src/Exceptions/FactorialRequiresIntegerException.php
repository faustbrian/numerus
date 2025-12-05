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
 * Exception thrown when factorial is attempted on a non-integer value.
 *
 * @author Brian Faust <brian@cline.sh>
 */
final class FactorialRequiresIntegerException extends InvalidArgumentException implements NumerusException
{
    public static function create(): self
    {
        return new self('Factorial requires an integer value');
    }
}
