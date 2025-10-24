<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Unit;

use Cline\Numerus\Adapters\BCMathAdapter;
use Cline\Numerus\Adapters\GMPAdapter;
use Cline\Numerus\Adapters\NativeMathAdapter;
use Cline\Numerus\MathDriver;

use function describe;
use function expect;
use function extension_loaded;
use function test;

describe('MathDriver', function (): void {
    test('creates Native adapter', function (): void {
        $adapter = MathDriver::Native->createAdapter();
        expect($adapter)->toBeInstanceOf(NativeMathAdapter::class);
    });

    test('creates BCMath adapter', function (): void {
        if (!extension_loaded('bcmath')) {
            $this->markTestSkipped('BCMath extension not available');
        }

        $adapter = MathDriver::BCMath->createAdapter();
        expect($adapter)->toBeInstanceOf(BCMathAdapter::class);
    });

    test('creates GMP adapter', function (): void {
        if (!extension_loaded('gmp')) {
            $this->markTestSkipped('GMP extension not available');
        }

        $adapter = MathDriver::GMP->createAdapter();
        expect($adapter)->toBeInstanceOf(GMPAdapter::class);
    });

    test('creates BCMath adapter with custom scale', function (): void {
        if (!extension_loaded('bcmath')) {
            $this->markTestSkipped('BCMath extension not available');
        }

        $adapter = MathDriver::BCMath->createAdapter(scale: 5);
        expect($adapter)->toBeInstanceOf(BCMathAdapter::class);
    });

    test('creates GMP adapter with custom scale', function (): void {
        if (!extension_loaded('gmp')) {
            $this->markTestSkipped('GMP extension not available');
        }

        $adapter = MathDriver::GMP->createAdapter(scale: 5);
        expect($adapter)->toBeInstanceOf(GMPAdapter::class);
    });

    test('Auto mode selects BCMath when available', function (): void {
        if (!extension_loaded('bcmath')) {
            $this->markTestSkipped('BCMath extension not available');
        }

        $adapter = MathDriver::Auto->createAdapter();
        expect($adapter)->toBeInstanceOf(BCMathAdapter::class);
    });

    test('tryFrom creates driver from string', function (): void {
        expect(MathDriver::tryFrom('native'))->toBe(MathDriver::Native);
        expect(MathDriver::tryFrom('auto'))->toBe(MathDriver::Auto);
        expect(MathDriver::tryFrom('bcmath'))->toBe(MathDriver::BCMath);
        expect(MathDriver::tryFrom('gmp'))->toBe(MathDriver::GMP);
        expect(MathDriver::tryFrom('invalid'))->toBeNull();
    });
});
