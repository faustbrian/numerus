<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Cline\Numerus\Numerus;

use function Cline\Numerus\numerus;

describe('numerus() Helper Function', function (): void {
    describe('Happy Path', function (): void {
        test('creates Numerus instance from integer', function (): void {
            $num = numerus(42);

            expect($num)->toBeInstanceOf(Numerus::class);
            expect($num->value())->toEqual(42);
        });

        test('creates Numerus instance from float', function (): void {
            $num = numerus(42.5);

            expect($num)->toBeInstanceOf(Numerus::class);
            expect($num->value())->toEqual(42.5);
        });

        test('creates Numerus instance from negative number', function (): void {
            $num = numerus(-100);

            expect($num)->toBeInstanceOf(Numerus::class);
            expect($num->value())->toEqual(-100);
        });

        test('creates Numerus instance from zero', function (): void {
            $num = numerus(0);

            expect($num)->toBeInstanceOf(Numerus::class);
            expect($num->value())->toEqual(0);
        });

        test('allows method chaining on created instance', function (): void {
            $result = numerus(10)
                ->plus(5)
                ->minus(3)
                ->multiplyBy(2);

            expect($result->value())->toEqual(24);
        });

        test('allows formatting methods on created instance', function (): void {
            expect(numerus(1_000)->abbreviate())->toBe('1K');
            expect(numerus(1_024)->fileSize())->toBe('1 KB');
            expect(numerus(10)->percentage())->toBe('10%');
        });

        test('is equivalent to Numerus::create()', function (): void {
            $fromHelper = numerus(42);
            $fromStatic = Numerus::create(42);

            expect($fromHelper->value())->toBe($fromStatic->value());
            expect($fromHelper)->toBeInstanceOf(Numerus::class);
            expect($fromStatic)->toBeInstanceOf(Numerus::class);
        });
    });

    describe('Edge Cases', function (): void {
        test('creates instance from very large integer', function (): void {
            $num = numerus(\PHP_INT_MAX);

            expect($num->value())->toBe(\PHP_INT_MAX);
        });

        test('creates instance from very small float', function (): void {
            $num = numerus(0.000_000_1);

            expect($num->value())->toBe(0.000_000_1);
        });

        test('creates instance from negative zero', function (): void {
            $num = numerus(-0.0);

            expect($num->value())->toEqual(0.0);
        });

        test('allows complex chaining operations', function (): void {
            $result = numerus(100)
                ->plus(50)
                ->divideBy(2)
                ->round(2)
                ->minus(25);

            expect($result->value())->toEqual(50.0);
        });

        test('can use with arithmetic operators', function (): void {
            $num1 = numerus(10);
            $num2 = numerus(5);

            $result = $num1->plus($num2);

            expect($result->value())->toEqual(15);
        });

        test('preserves immutability through helper', function (): void {
            $original = numerus(42);
            $modified = $original->plus(8);

            expect($original->value())->toEqual(42);
            expect($modified->value())->toEqual(50);
        });

        test('can use with comparison methods', function (): void {
            $num = numerus(50);

            expect($num->between(0, 100))->toBeTrue();
            expect($num->greaterThan(25))->toBeTrue();
            expect($num->lessThan(75))->toBeTrue();
        });

        test('can use with type checking methods', function (): void {
            expect(numerus(42)->isPositive())->toBeTrue();
            expect(numerus(0)->isZero())->toBeTrue();
            expect(numerus(-5)->isNegative())->toBeTrue();
            expect(numerus(4)->isEven())->toBeTrue();
            expect(numerus(5)->isOdd())->toBeTrue();
        });

        test('can use with mathematical operations', function (): void {
            expect(numerus(16)->sqrt()->value())->toEqual(4.0);
            expect(numerus(2)->power(3)->value())->toEqual(8);
            expect(numerus(10)->mod(3)->value())->toEqual(1);
        });

        test('can use with percentage operations', function (): void {
            expect(numerus(25)->percentOf(100))->toEqual(25.0);
            expect(numerus(100)->addPercent(20)->value())->toEqual(120.0);
            expect(numerus(100)->subtractPercent(20)->value())->toEqual(80.0);
        });

        test('can use with rounding operations', function (): void {
            expect(numerus(42.6)->round()->value())->toEqual(43.0);
            expect(numerus(42.3)->ceil()->value())->toEqual(43.0);
            expect(numerus(42.9)->floor()->value())->toEqual(42.0);
            expect(numerus(-42)->abs()->value())->toEqual(42);
        });

        test('can use with conversion methods', function (): void {
            expect(numerus(42.9)->toInt())->toEqual(42);
            expect(numerus(42)->toFloat())->toEqual(42.0);
            expect(numerus(42)->toString())->toBe('42');
        });

        test('can use string casting', function (): void {
            $num = numerus(42);

            expect((string) $num)->toBe('42');
        });

        test('works with all formatting methods', function (): void {
            expect(numerus(1_000_000)->abbreviate())->toBe('1M');
            expect(numerus(1_000_000)->forHumans())->toBe('1 million');
            expect(numerus(1_000)->format())->toBe('1,000');
            expect(numerus(1)->ordinal())->toBe('1st');
            expect(numerus(1)->spell())->toBe('one');
            expect(numerus(1)->spellOrdinal())->toBe('first');
        });

        test('helper maintains type safety', function (): void {
            $num = numerus(42);

            expect($num)->toBeInstanceOf(Numerus::class);
            expect($num->value())->toBeInt();

            $floatNum = numerus(42.5);

            expect($floatNum)->toBeInstanceOf(Numerus::class);
            expect($floatNum->value())->toBeFloat();
        });

        test('can create multiple independent instances', function (): void {
            $num1 = numerus(10);
            $num2 = numerus(20);
            $num3 = numerus(30);

            expect($num1->value())->toEqual(10);
            expect($num2->value())->toEqual(20);
            expect($num3->value())->toEqual(30);

            $result = $num1->plus($num2)->plus($num3);

            expect($result->value())->toEqual(60);
            expect($num1->value())->toEqual(10); // Original unchanged
        });
    });
});
