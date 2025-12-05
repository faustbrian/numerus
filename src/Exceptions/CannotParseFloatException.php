<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\Numerus\Exceptions;

use InvalidArgumentException;

use function sprintf;

/**
 * Exception thrown when a string cannot be parsed as a float.
 *
 * @author Brian Faust <brian@cline.sh>
 */
final class CannotParseFloatException extends InvalidArgumentException implements NumerusException
{
    public static function fromValue(string $value): self
    {
        return new self(sprintf("Unable to parse '%s' as float", $value));
    }
}
