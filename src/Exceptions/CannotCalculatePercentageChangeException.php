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
 * Exception thrown when percentage change calculation cannot be performed.
 *
 * @author Brian Faust <brian@cline.sh>
 */
final class CannotCalculatePercentageChangeException extends InvalidArgumentException implements NumerusException
{
    public static function fromZero(): self
    {
        return new self('Cannot calculate percentage change from zero');
    }
}
