<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\Numerus;

use Cline\Numerus\Adapters\BCMathAdapter;
use Cline\Numerus\Adapters\GMPAdapter;
use Cline\Numerus\Adapters\NativeMathAdapter;
use Cline\Numerus\Contracts\MathAdapter;

use function extension_loaded;

/**
 * Enumeration of available mathematical adapter drivers.
 *
 * Defines the supported math backends that can be used for numerical operations,
 * each with different performance characteristics and precision capabilities.
 * The Auto case automatically selects the best available adapter based on installed
 * PHP extensions, preferring BCMath for precision, GMP for performance, and falling
 * back to native PHP operations when no extensions are available.
 *
 * @author Brian Faust <brian@cline.sh>
 */
enum MathDriver: string
{
    /**
     * Automatically detect and use the best available adapter.
     *
     * Selection priority: BCMath > GMP > Native PHP
     */
    case Auto = 'auto';

    /**
     * Use native PHP math operations for maximum performance.
     *
     * Fast but subject to floating-point precision limitations.
     */
    case Native = 'native';

    /**
     * Use BCMath extension for arbitrary-precision decimal arithmetic.
     *
     * Slower but provides exact decimal calculations without rounding errors.
     */
    case BCMath = 'bcmath';

    /**
     * Use GMP extension for high-performance integer-based arithmetic.
     *
     * Fast with good precision through internal integer scaling.
     */
    case GMP = 'gmp';

    /**
     * Create a math adapter instance for this driver.
     *
     * Instantiates the appropriate adapter implementation based on the enum case,
     * configured with the specified precision scale. The Auto case delegates to
     * autoDetect() to choose the best available adapter.
     *
     * @param  int         $scale Number of decimal places to maintain in calculations (default: 10).
     *                            Only applicable to BCMath and GMP adapters; ignored for Native.
     * @return MathAdapter The configured math adapter instance
     */
    public function createAdapter(int $scale = 10): MathAdapter
    {
        return match ($this) {
            self::Auto => $this->autoDetect($scale),
            self::Native => new NativeMathAdapter(),
            self::BCMath => new BCMathAdapter($scale),
            self::GMP => new GMPAdapter($scale),
        };
    }

    /**
     * Automatically detect the best available math adapter.
     *
     * Checks for available PHP extensions in priority order and returns the most
     * capable adapter. BCMath is preferred for its decimal precision, followed by
     * GMP for performance, with native PHP math as the universal fallback.
     *
     * @param  int         $scale Number of decimal places for precision-aware adapters
     * @return MathAdapter The best available math adapter instance
     */
    private function autoDetect(int $scale): MathAdapter
    {
        if (extension_loaded('bcmath')) {
            return new BCMathAdapter($scale);
        }

        // @codeCoverageIgnoreStart
        if (extension_loaded('gmp')) {
            return new GMPAdapter($scale);
        }

        return new NativeMathAdapter();
        // @codeCoverageIgnoreEnd
    }
}
