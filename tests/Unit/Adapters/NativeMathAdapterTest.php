<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Unit\Adapters;

use Cline\Numerus\Adapters\NativeMathAdapter;
use RoundingMode;

use function beforeEach;
use function ceil;
use function describe;
use function expect;
use function floor;
use function sqrt;
use function test;

beforeEach(function (): void {
    $this->adapter = new NativeMathAdapter();
});

describe('Native Math Adapter', function (): void {
    describe('Type Preservation', function (): void {
        test('addition returns int when both operands result in whole number', function (): void {
            $result = $this->adapter->add(10, 5);
            expect($result)->toBe(15);
            expect($result)->toBeInt();
        });

        test('addition returns float when result has decimals', function (): void {
            $result = $this->adapter->add(10.5, 5.5);
            expect($result)->toBe(16.0);
            expect($result)->toBeFloat();
        });

        test('division returns int when evenly divisible', function (): void {
            $result = $this->adapter->divide(10, 2);
            expect($result)->toBe(5);
            expect($result)->toBeInt();
        });

        test('division returns float when not evenly divisible', function (): void {
            $result = $this->adapter->divide(10, 3);
            expect($result)->toBeFloat();
            expect($result)->toBeGreaterThan(3.33);
            expect($result)->toBeLessThan(3.34);
        });
    });

    describe('Arithmetic Operations', function (): void {
        test('adds two numbers', function (): void {
            expect($this->adapter->add(10, 5))->toBe(15);
            expect($this->adapter->add(10.5, 5.5))->toBe(16.0);
        });

        test('subtracts two numbers', function (): void {
            expect($this->adapter->subtract(10, 3))->toBe(7);
            expect($this->adapter->subtract(10.5, 3.5))->toBe(7.0);
        });

        test('multiplies two numbers', function (): void {
            expect($this->adapter->multiply(10, 5))->toBe(50);
            expect($this->adapter->multiply(10.5, 2))->toBe(21.0);
        });

        test('divides two numbers', function (): void {
            expect($this->adapter->divide(10, 2))->toBe(5);
            expect($this->adapter->divide(10, 3))->toBeFloat();
        });

        test('calculates modulo', function (): void {
            expect($this->adapter->mod(10, 3))->toBe(1);
            expect($this->adapter->mod(10, 4))->toBe(2);
        });
    });

    describe('Rounding Operations', function (): void {
        test('rounds with HalfAwayFromZero', function (): void {
            expect($this->adapter->round(2.5, 0, RoundingMode::HalfAwayFromZero))->toBe(3.0);
            expect($this->adapter->round(-2.5, 0, RoundingMode::HalfAwayFromZero))->toBe(-3.0);
        });

        test('rounds with PositiveInfinity (ceiling)', function (): void {
            expect($this->adapter->round(2.1, 0, RoundingMode::PositiveInfinity))->toBe(3.0);
            expect($this->adapter->round(-2.9, 0, RoundingMode::PositiveInfinity))->toBe(-2.0);
        });

        test('rounds with NegativeInfinity (floor)', function (): void {
            expect($this->adapter->round(2.9, 0, RoundingMode::NegativeInfinity))->toBe(2.0);
            expect($this->adapter->round(-2.1, 0, RoundingMode::NegativeInfinity))->toBe(-3.0);
        });

        test('rounds with precision', function (): void {
            expect($this->adapter->round(2.456, 2, RoundingMode::HalfAwayFromZero))->toBe(2.46);
        });

        test('round always returns float', function (): void {
            $result = $this->adapter->round(5.0, 0, RoundingMode::HalfAwayFromZero);
            expect($result)->toBe(5.0);
            expect($result)->toBeFloat();
        });
    });

    describe('Ceil and Floor', function (): void {
        test('ceil always returns float', function (): void {
            expect($this->adapter->ceil(2.1))->toBe(3.0);
            expect($this->adapter->ceil(2.1))->toBeFloat();
            expect($this->adapter->ceil(-2.9))->toBe(-2.0);
        });

        test('floor always returns float', function (): void {
            expect($this->adapter->floor(2.9))->toBe(2.0);
            expect($this->adapter->floor(2.9))->toBeFloat();
            expect($this->adapter->floor(-2.1))->toBe(-3.0);
        });

        test('ceil and floor match PHP native behavior', function (): void {
            expect($this->adapter->ceil(5))->toBe(ceil(5));
            expect($this->adapter->floor(5))->toBe(floor(5));
        });
    });

    describe('Mathematical Functions', function (): void {
        test('calculates absolute value preserving type', function (): void {
            expect($this->adapter->abs(-42))->toBe(42);
            expect($this->adapter->abs(-42))->toBeInt();
            expect($this->adapter->abs(-42.5))->toBe(42.5);
            expect($this->adapter->abs(-42.5))->toBeFloat();
        });

        test('sqrt always returns float', function (): void {
            expect($this->adapter->sqrt(16))->toBe(4.0);
            expect($this->adapter->sqrt(16))->toBeFloat();
            expect($this->adapter->sqrt(2))->toBeGreaterThan(1.41);
            expect($this->adapter->sqrt(2))->toBeLessThan(1.42);
        });

        test('calculates power preserving type', function (): void {
            expect($this->adapter->power(2, 3))->toBe(8);
            expect($this->adapter->power(2, 3))->toBeInt();
            expect($this->adapter->power(2.5, 2))->toBe(6.25);
            expect($this->adapter->power(2.5, 2))->toBeFloat();
        });

        test('negates number preserving type', function (): void {
            expect($this->adapter->negate(42))->toBe(-42);
            expect($this->adapter->negate(42))->toBeInt();
            expect($this->adapter->negate(42.5))->toBe(-42.5);
            expect($this->adapter->negate(42.5))->toBeFloat();
        });
    });

    describe('Comparison Operations', function (): void {
        test('compares numbers', function (): void {
            expect($this->adapter->compare(10, 5))->toBe(1);
            expect($this->adapter->compare(5, 10))->toBe(-1);
            expect($this->adapter->compare(10, 10))->toBe(0);
        });

        test('finds minimum preserving type', function (): void {
            expect($this->adapter->min(10, 5))->toBe(5);
            expect($this->adapter->min(10, 5))->toBeInt();
            expect($this->adapter->min(10.5, 5.5))->toBe(5.5);
            expect($this->adapter->min(10.5, 5.5))->toBeFloat();
        });

        test('finds maximum preserving type', function (): void {
            expect($this->adapter->max(10, 5))->toBe(10);
            expect($this->adapter->max(10, 5))->toBeInt();
            expect($this->adapter->max(10.5, 5.5))->toBe(10.5);
            expect($this->adapter->max(10.5, 5.5))->toBeFloat();
        });
    });

    describe('Integer and Fractional Parts', function (): void {
        test('extracts integer part', function (): void {
            expect($this->adapter->integerPart(12.34))->toBe(12);
            expect($this->adapter->integerPart(-12.34))->toBe(-12);
        });

        test('extracts fractional part', function (): void {
            $result = $this->adapter->fractionalPart(12.34);
            expect($result)->toBeGreaterThan(0.33);
            expect($result)->toBeLessThan(0.35);
        });

        test('fractional part returns 0 for integers', function (): void {
            expect($this->adapter->fractionalPart(12))->toBe(0);
            expect($this->adapter->fractionalPart(12))->toBeInt();
        });
    });

    describe('String Input Handling', function (): void {
        test('handles string inputs', function (): void {
            expect($this->adapter->add('10', '5'))->toBe(15);
            expect($this->adapter->add('10.5', '5.5'))->toBe(16.0);
        });

        test('converts string to appropriate type', function (): void {
            expect($this->adapter->add('10', '5'))->toBeInt();
            expect($this->adapter->add('10.5', '5.5'))->toBeFloat();
        });
    });

    describe('PHP Native Behavior Matching', function (): void {
        test('matches native addition operator', function (): void {
            expect($this->adapter->add(10, 5))->toBe(10 + 5);
            expect($this->adapter->add(10.5, 5.5))->toBe(10.5 + 5.5);
        });

        test('matches native division operator', function (): void {
            expect($this->adapter->divide(10, 2))->toBe(10 / 2);
            expect($this->adapter->divide(10, 3))->toBe(10 / 3);
        });

        test('matches native ceil function', function (): void {
            expect($this->adapter->ceil(2.1))->toBe(ceil(2.1));
            expect($this->adapter->ceil(5))->toBe(ceil(5));
        });

        test('matches native floor function', function (): void {
            expect($this->adapter->floor(2.9))->toBe(floor(2.9));
            expect($this->adapter->floor(5))->toBe(floor(5));
        });

        test('matches native sqrt function', function (): void {
            expect($this->adapter->sqrt(16))->toBe(sqrt(16));
            expect($this->adapter->sqrt(2))->toBe(sqrt(2));
        });
    });
});
