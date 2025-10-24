<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Unit\Adapters;

use Cline\Numerus\Adapters\BCMathAdapter;
use RoundingMode;

use function beforeEach;
use function describe;
use function expect;
use function extension_loaded;
use function test;

beforeEach(function (): void {
    if (!extension_loaded('bcmath')) {
        $this->markTestSkipped('BCMath extension not available');
    }

    $this->adapter = new BCMathAdapter();
});

describe('BCMath Adapter', function (): void {
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

        test('handles decimal precision', function (): void {
            $result = $this->adapter->add('0.1', '0.2');
            expect($result)->toBeString();
            expect((float) $result)->toBe(0.3);
        });

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

        test('rounds with HalfTowardsZero', function (): void {
            expect((float) $this->adapter->round(2.5, 0, RoundingMode::HalfTowardsZero))->toBe(2.0);
            expect((float) $this->adapter->round(-2.5, 0, RoundingMode::HalfTowardsZero))->toBe(-2.0);
        });

        test('rounds with HalfEven', function (): void {
            expect((float) $this->adapter->round(2.5, 0, RoundingMode::HalfEven))->toBe(2.0);
            expect((float) $this->adapter->round(3.5, 0, RoundingMode::HalfEven))->toBe(4.0);
            expect((float) $this->adapter->round(2.7, 0, RoundingMode::HalfEven))->toBe(3.0);
            expect((float) $this->adapter->round(-2.7, 0, RoundingMode::HalfEven))->toBe(-3.0);
        });

        test('rounds with HalfOdd', function (): void {
            expect((float) $this->adapter->round(2.5, 0, RoundingMode::HalfOdd))->toBe(3.0);
            expect((float) $this->adapter->round(3.5, 0, RoundingMode::HalfOdd))->toBe(3.0);
            expect((float) $this->adapter->round(2.7, 0, RoundingMode::HalfOdd))->toBe(3.0);
            expect((float) $this->adapter->round(-2.7, 0, RoundingMode::HalfOdd))->toBe(-3.0);
        });

        test('rounds with PositiveInfinity (ceiling)', function (): void {
            expect((float) $this->adapter->round(2.1, 0, RoundingMode::PositiveInfinity))->toBe(3.0);
            expect((float) $this->adapter->round(-2.9, 0, RoundingMode::PositiveInfinity))->toBe(-2.0);
        });

        test('rounds with NegativeInfinity (floor)', function (): void {
            expect((float) $this->adapter->round(2.9, 0, RoundingMode::NegativeInfinity))->toBe(2.0);
            expect((float) $this->adapter->round(-2.1, 0, RoundingMode::NegativeInfinity))->toBe(-3.0);
        });

        test('rounds with AwayFromZero', function (): void {
            expect((float) $this->adapter->round(2.1, 0, RoundingMode::AwayFromZero))->toBe(3.0);
            expect((float) $this->adapter->round(-2.1, 0, RoundingMode::AwayFromZero))->toBe(-3.0);
        });

        test('rounds with TowardsZero', function (): void {
            expect((float) $this->adapter->round(2.9, 0, RoundingMode::TowardsZero))->toBe(2.0);
            expect((float) $this->adapter->round(-2.9, 0, RoundingMode::TowardsZero))->toBe(-2.0);
        });

        test('rounds with precision', function (): void {
            $result = $this->adapter->round(2.456, 2, RoundingMode::HalfAwayFromZero);
            expect((float) $result)->toBe(2.46);
        });

        test('rounds fractional parts less than 0.5 with HalfEven', function (): void {
            expect((float) $this->adapter->round(2.3, 0, RoundingMode::HalfEven))->toBe(2.0);
            expect((float) $this->adapter->round(-2.3, 0, RoundingMode::HalfEven))->toBe(-2.0);
        });

        test('rounds fractional parts less than 0.5 with HalfOdd', function (): void {
            expect((float) $this->adapter->round(2.3, 0, RoundingMode::HalfOdd))->toBe(2.0);
            expect((float) $this->adapter->round(-2.3, 0, RoundingMode::HalfOdd))->toBe(-2.0);
        });

        test('rounds fractional parts greater than 0.5 with HalfTowardsZero', function (): void {
            expect((float) $this->adapter->round(2.7, 0, RoundingMode::HalfTowardsZero))->toBe(3.0);
            expect((float) $this->adapter->round(-2.7, 0, RoundingMode::HalfTowardsZero))->toBe(-3.0);
        });

        test('rounds whole numbers with AwayFromZero', function (): void {
            expect((float) $this->adapter->round(2.0, 0, RoundingMode::AwayFromZero))->toBe(2.0);
            expect((float) $this->adapter->round(-2.0, 0, RoundingMode::AwayFromZero))->toBe(-2.0);
        });

        test('rounds whole numbers with HalfAwayFromZero', function (): void {
            expect((float) $this->adapter->round(2.0, 0, RoundingMode::HalfAwayFromZero))->toBe(2.0);
            expect((float) $this->adapter->round(-2.0, 0, RoundingMode::HalfAwayFromZero))->toBe(-2.0);
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
            expect((float) $this->adapter->sqrt(2))->toBeGreaterThan(1.41);
            expect((float) $this->adapter->sqrt(2))->toBeLessThan(1.42);
        });

        test('calculates power', function (): void {
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

        test('extracts integer part from whole number', function (): void {
            expect($this->adapter->integerPart(42))->toBe(42);
            expect($this->adapter->integerPart(-42))->toBe(-42);
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

        test('handles very precise decimal strings', function (): void {
            $result = $this->adapter->add('0.0000000001', '0.0000000002');
            expect($result)->toBeString();
            expect((float) $result)->toBeGreaterThan(0.0);
        });
    });
});
