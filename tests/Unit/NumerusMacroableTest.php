<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Cline\Numerus\Numerus;

use function Cline\Numerus\numerus;

describe('Macroable', function (): void {
    describe('Happy Path', function (): void {
        test('can add instance macro', function (): void {
            Numerus::macro('squared', fn () => $this->multiplyBy($this));

            $result = numerus(5)->squared();

            expect($result->value())->toBe(25);
        });

        test('can add static macro', function (): void {
            Numerus::macro('fromHundred', fn (int|float $value): Numerus => Numerus::create(100)->minus($value));

            $result = Numerus::fromHundred(30);

            expect($result->value())->toBe(70);
        });

        test('can add macro with parameters', function (): void {
            Numerus::macro('multiplyAndAdd', fn (int|float $multiplier, int|float $addend) => $this->multiplyBy($multiplier)->plus($addend));

            $result = numerus(10)->multiplyAndAdd(3, 5);

            expect($result->value())->toBe(35);
        });

        test('macros work across all instances', function (): void {
            Numerus::macro('doubled', fn () => $this->multiplyBy(2));

            $first = numerus(5)->doubled();
            $second = numerus(10)->doubled();

            expect($first->value())->toBe(10);
            expect($second->value())->toBe(20);
        });

        test('can chain macros with native methods', function (): void {
            Numerus::macro('cubed', fn () => $this->multiplyBy($this)->multiplyBy($this));

            $result = numerus(3)->cubed()->plus(1);

            expect($result->value())->toBe(28);
        });

        test('can check if macro exists', function (): void {
            Numerus::macro('testMacro', fn (): object => $this);

            expect(Numerus::hasMacro('testMacro'))->toBeTrue();
            expect(Numerus::hasMacro('nonexistentMacro'))->toBeFalse();
        });

        test('macros preserve immutability', function (): void {
            Numerus::macro('increment', fn () => $this->plus(1));

            $original = numerus(10);
            $incremented = $original->increment();

            expect($original->value())->toBe(10);
            expect($incremented->value())->toBe(11);
        });

        test('can use macro with closure binding', function (): void {
            Numerus::macro('isGreaterThanTen', fn () => $this->greaterThan(10));

            expect(numerus(15)->isGreaterThanTen())->toBeTrue();
            expect(numerus(5)->isGreaterThanTen())->toBeFalse();
        });
    });

    describe('Edge Cases', function (): void {
        test('macro with zero value', function (): void {
            Numerus::macro('halved', fn () => $this->divideBy(2));

            $result = numerus(0)->halved();

            expect($result->value())->toBe(0);
        });

        test('macro with negative values', function (): void {
            Numerus::macro('absoluteValue', fn () => $this->abs());

            $result = numerus(-42)->absoluteValue();

            expect($result->value())->toBe(42);
        });

        test('macro returning non-Numerus value', function (): void {
            Numerus::macro('toArray', fn (): array => ['value' => $this->value()]);

            $result = numerus(42)->toArray();

            expect($result)->toBe(['value' => 42]);
        });
    });
});
