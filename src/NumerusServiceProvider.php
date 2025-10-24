<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\Numerus;

use Cline\Numerus\Contracts\MathAdapter;
use Illuminate\Contracts\Foundation\Application;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

use function assert;
use function config;
use function is_int;
use function is_string;

/**
 * Laravel service provider for the Numerus package.
 *
 * Registers the package configuration and binds the configured math adapter
 * implementation to Laravel's service container. The adapter selection and
 * precision scale are determined by the published configuration file.
 *
 * The math adapter is registered as a singleton, ensuring the same instance
 * is reused throughout the application lifecycle for consistent behavior and
 * optimal performance.
 *
 * @author Brian Faust <brian@cline.sh>
 */
final class NumerusServiceProvider extends PackageServiceProvider
{
    /**
     * Configure the package.
     *
     * Registers the package name and configuration file for publishing.
     * The configuration file can be published using:
     * php artisan vendor:publish --provider="Cline\Numerus\NumerusServiceProvider"
     */
    public function configurePackage(Package $package): void
    {
        $package
            ->name('numerus')
            ->hasConfigFile();
    }

    /**
     * Register package services after registration.
     *
     * Binds the MathAdapter interface to a singleton instance configured based
     * on the package configuration. The driver and scale settings are read from
     * the config file, with defaults of 'auto' driver and scale of 10.
     *
     * The driver value can be either a MathDriver enum case or a string that
     * matches an enum value. Invalid driver strings fall back to Auto mode.
     */
    public function packageRegistered(): void
    {
        $this->app->singleton(function (Application $app): MathAdapter {
            $driver = config('numerus.driver', MathDriver::Auto);
            $scale = config('numerus.scale', 10);

            if (is_string($driver)) {
                $driver = MathDriver::tryFrom($driver) ?? MathDriver::Auto;
            }

            assert($driver instanceof MathDriver);
            assert(is_int($scale));

            return $driver->createAdapter($scale);
        });
    }
}
