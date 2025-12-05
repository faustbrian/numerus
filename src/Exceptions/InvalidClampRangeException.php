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
 * Exception thrown when clamp range is invalid (min > max).
 *
 * @author Brian Faust <brian@cline.sh>
 */
final class InvalidClampRangeException extends InvalidArgumentException implements NumerusException
{
    public static function minGreaterThanMax(): self
    {
        return new self('Min value cannot be greater than max value');
    }
}
