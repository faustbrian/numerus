<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Exceptions;

use Exception;

/**
 * Exception used for testing exception handling behavior.
 *
 * @author Brian Faust <brian@cline.sh>
 */
final class SimulatedTestException extends Exception
{
    public static function create(): self
    {
        return new self('Test exception');
    }
}
