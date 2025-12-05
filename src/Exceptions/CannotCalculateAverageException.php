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
 * @author Brian Faust <brian@cline.sh>
 */
final class CannotCalculateAverageException extends InvalidArgumentException implements NumerusException
{
    public static function emptyArray(): self
    {
        return new self('Cannot calculate average of empty array');
    }
}
