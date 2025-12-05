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
 * Exception thrown when LCM calculation cannot be performed with zero.
 *
 * @author Brian Faust <brian@cline.sh>
 */
final class CannotCalculateLcmException extends InvalidArgumentException implements NumerusException
{
    public static function withZero(): self
    {
        return new self('Cannot calculate LCM with zero');
    }
}
