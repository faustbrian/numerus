<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Cline\Numerus\Numerus;
use Cline\Numerus\Percentage;

use function Cline\Numerus\numerus;

describe('Macroable', function (): void {
    describe('Happy Path', function (): void {
        test('can add static macro', function (): void {
            Percentage::macro('isHigh', fn (float $value, float $threshold = 50): bool => $value >= $threshold);

            $result = Percentage::isHigh(75);

            expect($result)->toBeTrue();
        });

        test('can add macro with custom logic', function (): void {
            Percentage::macro('discountPrice', fn (float $percentage, float $price): float => Percentage::subtract($percentage, $price));

            $result = Percentage::discountPrice(20, 100);

            expect($result)->toBe(80.0);
        });

        test('can add macro with multiple parameters', function (): void {
            Percentage::macro('markup', fn (float $cost, float $markupPercent): float => Percentage::add($markupPercent, $cost));

            $result = Percentage::markup(100, 25);

            expect($result)->toBe(125.0);
        });

        test('can check if macro exists', function (): void {
            Percentage::macro('testMacro', fn (): true => true);

            expect(Percentage::hasMacro('testMacro'))->toBeTrue();
            expect(Percentage::hasMacro('nonexistentMacro'))->toBeFalse();
        });

        test('can add macro that wraps existing methods', function (): void {
            Percentage::macro('growth', fn (float $old, float $new): float => Percentage::differenceBetween($old, $new));

            $result = Percentage::growth(50, 75);

            expect($result)->toBe(50.0);
        });

        test('macro can accept Numerus instances', function (): void {
            Percentage::macro('taxAmount', fn (float $taxRate, int|float|Numerus $price): float => Percentage::calculate($taxRate, $price));

            $result = Percentage::taxAmount(10, numerus(100));

            expect($result)->toBe(10.0);
        });
    });

    describe('Edge Cases', function (): void {
        test('macro with zero percentage', function (): void {
            Percentage::macro('applyDiscount', fn (float $discount, float $price): float => Percentage::subtract($discount, $price));

            $result = Percentage::applyDiscount(0, 100);

            expect($result)->toBe(100.0);
        });

        test('macro with hundred percent', function (): void {
            Percentage::macro('removeAll', fn (float $price): float => Percentage::subtract(100, $price));

            $result = Percentage::removeAll(100);

            expect($result)->toBe(0.0);
        });

        test('macro returning boolean', function (): void {
            Percentage::macro('isSignificant', fn (float $percentage): bool => abs($percentage) >= 10);

            expect(Percentage::isSignificant(15))->toBeTrue();
            expect(Percentage::isSignificant(5))->toBeFalse();
            expect(Percentage::isSignificant(-15))->toBeTrue();
        });
    });
});
