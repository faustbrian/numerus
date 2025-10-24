<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\Numerus\Facades;

use Cline\Numerus\Contracts\MathAdapter;
use Illuminate\Support\Facades\Facade;
use RoundingMode;

/**
 * Facade for mathematical operations using the configured adapter.
 *
 * Provides static access to math adapter methods for convenient use throughout
 * the application. The underlying adapter is resolved from Laravel's service
 * container based on the package configuration, allowing centralized control
 * over which math backend is used.
 *
 * All methods delegate to the configured MathAdapter implementation, which may
 * be BCMath, GMP, or native PHP depending on configuration and available extensions.
 *
 * @method static int|float|string abs(int|float|string $value)                                                                              Calculate absolute value
 * @method static int|float|string add(int|float|string $a, int|float|string $b)                                                             Add two numbers
 * @method static int              ceil(int|float|string $value)                                                                             Round up to nearest integer
 * @method static int|float|string divide(int|float|string $a, int|float|string $b)                                                          Divide two numbers
 * @method static int              floor(int|float|string $value)                                                                            Round down to nearest integer
 * @method static float|string     fractionalPart(int|float|string $value)                                                                   Extract fractional part
 * @method static int              integerPart(int|float|string $value)                                                                      Extract integer part
 * @method static int|float|string max(int|float|string $a, int|float|string $b)                                                             Get maximum of two numbers
 * @method static int|float|string min(int|float|string $a, int|float|string $b)                                                             Get minimum of two numbers
 * @method static int|float|string mod(int|float|string $a, int|float|string $b)                                                             Calculate modulo
 * @method static int|float|string multiply(int|float|string $a, int|float|string $b)                                                        Multiply two numbers
 * @method static int|float|string negate(int|float|string $value)                                                                           Negate a number
 * @method static int              compare(int|float|string $a, int|float|string $b)                          Compare two numbers (-1, 0, 1)
 * @method static int|float|string power(int|float|string $base, int|float $exponent)                                                        Raise to power
 * @method static int|float|string round(int|float|string $value, int $precision, ?RoundingMode $mode)                                       Round to precision
 * @method static int|float|string sqrt(int|float|string $value)                                                                             Calculate square root
 * @method static int|float|string subtract(int|float|string $a, int|float|string $b)                                                        Subtract two numbers
 *
 * @see MathAdapter
 *
 * @author Brian Faust <brian@cline.sh>
 */
final class Math extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * Returns the service container binding key for the math adapter, which
     * is resolved based on the package configuration.
     *
     * @return string The facade accessor key
     */
    protected static function getFacadeAccessor(): string
    {
        return MathAdapter::class;
    }
}
