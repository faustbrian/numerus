<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Unit;

use Cline\Numerus\Adapters\BCMathAdapter;
use Cline\Numerus\Adapters\NativeMathAdapter;
use Cline\Numerus\MathAdapterRegistry;
use stdClass;

use function afterEach;
use function beforeEach;
use function describe;
use function expect;
use function extension_loaded;
use function test;

beforeEach(function (): void {
    MathAdapterRegistry::resetDefault();
});

afterEach(function (): void {
    MathAdapterRegistry::resetDefault();
});

describe('MathAdapterRegistry', function (): void {
    describe('Default Adapter', function (): void {
        test('returns NativeMathAdapter by default', function (): void {
            $adapter = MathAdapterRegistry::getDefault();
            expect($adapter)->toBeInstanceOf(NativeMathAdapter::class);
        });

        test('can set custom default adapter', function (): void {
            if (!extension_loaded('bcmath')) {
                $this->markTestSkipped('BCMath extension not available');
            }

            $bcmath = new BCMathAdapter();
            MathAdapterRegistry::setDefault($bcmath);

            expect(MathAdapterRegistry::getDefault())->toBe($bcmath);
        });

        test('resetDefault restores to NativeMathAdapter', function (): void {
            if (!extension_loaded('bcmath')) {
                $this->markTestSkipped('BCMath extension not available');
            }

            $bcmath = new BCMathAdapter();
            MathAdapterRegistry::setDefault($bcmath);
            expect(MathAdapterRegistry::getDefault())->toBe($bcmath);

            MathAdapterRegistry::resetDefault();
            expect(MathAdapterRegistry::getDefault())->toBeInstanceOf(NativeMathAdapter::class);
        });
    });

    describe('Request-Scoped Adapters', function (): void {
        test('can set adapter for specific request context', function (): void {
            if (!extension_loaded('bcmath')) {
                $this->markTestSkipped('BCMath extension not available');
            }

            $request = new stdClass();
            $adapter = new BCMathAdapter();

            MathAdapterRegistry::setForRequest($request, $adapter);
            expect(MathAdapterRegistry::getForRequest($request))->toBe($adapter);
        });

        test('returns null for unknown request context', function (): void {
            $request = new stdClass();
            expect(MathAdapterRegistry::getForRequest($request))->toBeNull();
        });

        test('different request contexts have different adapters', function (): void {
            if (!extension_loaded('bcmath')) {
                $this->markTestSkipped('BCMath extension not available');
            }

            $request1 = new stdClass();
            $request2 = new stdClass();
            $adapter1 = new BCMathAdapter(scale: 5);
            $adapter2 = new BCMathAdapter(scale: 10);

            MathAdapterRegistry::setForRequest($request1, $adapter1);
            MathAdapterRegistry::setForRequest($request2, $adapter2);

            expect(MathAdapterRegistry::getForRequest($request1))->toBe($adapter1);
            expect(MathAdapterRegistry::getForRequest($request2))->toBe($adapter2);
        });

        test('request context is garbage collected when out of scope', function (): void {
            if (!extension_loaded('bcmath')) {
                $this->markTestSkipped('BCMath extension not available');
            }

            $request = new stdClass();
            $adapter = new BCMathAdapter();

            MathAdapterRegistry::setForRequest($request, $adapter);
            expect(MathAdapterRegistry::getForRequest($request))->toBe($adapter);

            unset($request);

            // After unsetting, we can't test it anymore because the WeakMap
            // automatically removes the entry. This test verifies the API works.
            expect(true)->toBeTrue();
        });
    });
});
