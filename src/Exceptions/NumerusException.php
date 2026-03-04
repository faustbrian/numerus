<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\Numerus\Exceptions;

use Throwable;

/**
 * Marker interface for all Numerus package exceptions.
 *
 * Extend Throwable to allow consumers to catch any exception thrown by
 * the Numerus package with a single catch block, regardless of the
 * specific exception type.
 *
 * ```php
 * try {
 *     numerus(0)->divideBy(0);
 * } catch (NumerusException $e) {
 *     // Catches any Numerus-specific exception
 * }
 * ```
 *
 * @author Brian Faust <brian@cline.sh>
 *
 * @since 1.0.0
 */
interface NumerusException extends Throwable {}
