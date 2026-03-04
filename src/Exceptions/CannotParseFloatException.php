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
 * Raised when an input string does not represent a valid floating-point number,
 * such as non-numeric strings or values that exceed PHP float precision bounds.
 *
 * @author Brian Faust <brian@cline.sh>
 *
 * @since 1.0.0
 */
final class CannotParseFloatException extends InvalidArgumentException implements NumerusException
{
    /**
     * Create an exception for a failed float parse attempt on a specific string value.
     *
     * @param string $value The string that could not be parsed as a float
     *
     * @return self The exception instance with a message identifying the invalid value
     */
    public static function fromValue(string $value): self
    {
        return new self(sprintf("Unable to parse '%s' as float", $value));
    }
}
