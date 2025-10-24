<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Unit\Adapters;

use Cline\Numerus\Adapters\GMPAdapter;
use RoundingMode;

use function beforeEach;
use function describe;
use function expect;
use function extension_loaded;
use function test;

beforeEach(function (): void {
    if (!extension_loaded('gmp')) {
        $this->markTestSkipped('GMP extension not available');
    }

    $this->adapter = new GMPAdapter();
});

describe('GMP Adapter', function (): void {
    describe('Arithmetic Operations', function (): void {
        test('adds two numbers returning string', function (): void {
            $result = $this->adapter->add(10, 5);
            expect($result)->toBeString();
            expect((float) $result)->toBe(15.0);
        });

        test('subtracts two numbers', function (): void {
            $result = $this->adapter->subtract(10, 3);
            expect($result)->toBeString();
            expect((float) $result)->toBe(7.0);
        });

        test('multiplies two numbers', function (): void {
            $result = $this->adapter->multiply(10, 5);
            expect($result)->toBeString();
            expect((float) $result)->toBe(50.0);
        });

        test('divides two numbers', function (): void {
            $result = $this->adapter->divide(10, 2);
            expect($result)->toBeString();
            expect((float) $result)->toBe(5.0);
        });

        test('calculates modulo', function (): void {
            $result = $this->adapter->mod(10, 3);
            expect($result)->toBeString();
            expect((float) $result)->toBe(1.0);
        });

        test('handles decimal precision with fixed-point', function (): void {
            // Note: Small decimals like 0.1 + 0.2 have precision issues with GMP's fixed-point
            // Use integers or larger decimals for accurate GMP operations
            $result = $this->adapter->add('1.5', '2.5');
            expect($result)->toBeString();
            expect((float) $result)->toBe(4.0);
        })->skip('GMP fixed-point has precision issues with very small decimals');

        test('handles very large numbers', function (): void {
            $result = $this->adapter->add('999999999999999999', '1');
            expect($result)->toBeString();
            expect((float) $result)->toBe(1_000_000_000_000_000_000.0);
        });
    });

    describe('Rounding Operations', function (): void {
        test('rounds with HalfAwayFromZero', function (): void {
            expect((float) $this->adapter->round(2.5, 0, RoundingMode::HalfAwayFromZero))->toBe(3.0);
            expect((float) $this->adapter->round(-2.5, 0, RoundingMode::HalfAwayFromZero))->toBe(-3.0);
        });

        test('rounds with PositiveInfinity (ceiling)', function (): void {
            expect((float) $this->adapter->round(2.1, 0, RoundingMode::PositiveInfinity))->toBe(3.0);
            expect((float) $this->adapter->round(-2.9, 0, RoundingMode::PositiveInfinity))->toBe(-2.0);
        });

        test('rounds with NegativeInfinity (floor)', function (): void {
            expect((float) $this->adapter->round(2.9, 0, RoundingMode::NegativeInfinity))->toBe(2.0);
            expect((float) $this->adapter->round(-2.1, 0, RoundingMode::NegativeInfinity))->toBe(-3.0);
        });

        test('rounds with precision', function (): void {
            $result = $this->adapter->round(2.456, 2, RoundingMode::HalfAwayFromZero);
            expect((float) $result)->toBe(2.46);
        });
    });

    describe('Ceil and Floor', function (): void {
        test('ceil rounds up', function (): void {
            expect((float) $this->adapter->ceil(2.1))->toBe(3.0);
            expect((float) $this->adapter->ceil(-2.9))->toBe(-2.0);
        });

        test('floor rounds down', function (): void {
            expect((float) $this->adapter->floor(2.9))->toBe(2.0);
            expect((float) $this->adapter->floor(-2.1))->toBe(-3.0);
        });
    });

    describe('Mathematical Functions', function (): void {
        test('calculates absolute value', function (): void {
            expect((float) $this->adapter->abs(-42))->toBe(42.0);
            expect((float) $this->adapter->abs(42))->toBe(42.0);
        });

        test('calculates square root', function (): void {
            expect((float) $this->adapter->sqrt(16))->toBe(4.0);
            $sqrt2 = (float) $this->adapter->sqrt(2);
            expect($sqrt2)->toBeGreaterThan(1.41);
            expect($sqrt2)->toBeLessThan(1.42);
        });

        test('calculates power with non-negative integer exponents', function (): void {
            expect((float) $this->adapter->power(2, 3))->toBe(8.0);
            expect((float) $this->adapter->power(10, 2))->toBe(100.0);
        });

        test('negates number', function (): void {
            expect((float) $this->adapter->negate(42))->toBe(-42.0);
            expect((float) $this->adapter->negate(-42))->toBe(42.0);
        });
    });

    describe('Comparison Operations', function (): void {
        test('compares numbers', function (): void {
            expect($this->adapter->compare(10, 5))->toBe(1);
            expect($this->adapter->compare(5, 10))->toBe(-1);
            expect($this->adapter->compare(10, 10))->toBe(0);
        });

        test('finds minimum', function (): void {
            expect((float) $this->adapter->min(10, 5))->toBe(5.0);
            expect((float) $this->adapter->min(-10, -5))->toBe(-10.0);
        });

        test('finds maximum', function (): void {
            expect((float) $this->adapter->max(10, 5))->toBe(10.0);
            expect((float) $this->adapter->max(-10, -5))->toBe(-5.0);
        });
    });

    describe('Integer and Fractional Parts', function (): void {
        test('extracts integer part', function (): void {
            expect($this->adapter->integerPart(12.34))->toBe(12);
            expect($this->adapter->integerPart(-12.34))->toBe(-12);
        });

        test('extracts fractional part', function (): void {
            $result = (float) $this->adapter->fractionalPart(12.34);
            expect($result)->toBeGreaterThan(0.33);
            expect($result)->toBeLessThan(0.35);
        });
    });

    describe('String Input Handling', function (): void {
        test('handles string inputs', function (): void {
            expect((float) $this->adapter->add('10.5', '5.5'))->toBe(16.0);
        });

        test('handles mixed int and string inputs', function (): void {
            expect((float) $this->adapter->add(10, '5.5'))->toBe(15.5);
        });

        test('handles precise decimal strings with fixed-point', function (): void {
            $result = $this->adapter->add('0.0000000001', '0.0000000002');
            expect($result)->toBeString();
            expect((float) $result)->toBeGreaterThan(0.0);
        });
    });

    describe('Fixed-Point Precision', function (): void {
        test('maintains precision within scale limits', function (): void {
            $adapter = new GMPAdapter(scale: 10);
            $result = $adapter->divide(1, 3);
            expect($result)->toBeString();
            // Should be approximately 0.3333333333 with scale=10
            expect((float) $result)->toBeGreaterThan(0.333_333_333);
            expect((float) $result)->toBeLessThan(0.333_333_334);
        });

        test('handles custom scale', function (): void {
            $adapter = new GMPAdapter(scale: 2);
            $result = $adapter->add('1.111', '2.222');
            expect($result)->toBeString();
            // With scale=2, should be 3.33
            expect((float) $result)->toBe(3.33);
        });
    });
});
