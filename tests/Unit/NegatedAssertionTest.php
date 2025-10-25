<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Cline\Numerus\Numerus;

use function Cline\Numerus\numerus;

describe('NegatedAssertion', function (): void {
    describe('equals', function (): void {
        test('inverts equality check - returns true when not equal', function (): void {
            expect(numerus(10)->not()->equals(5))->toBeTrue();
            expect(numerus(10)->not()->equals(15))->toBeTrue();
            expect(numerus(-5)->not()->equals(5))->toBeTrue();
        });

        test('inverts equality check - returns false when equal', function (): void {
            expect(numerus(10)->not()->equals(10))->toBeFalse();
            expect(numerus(0)->not()->equals(0))->toBeFalse();
            expect(numerus(-5)->not()->equals(-5))->toBeFalse();
        });

        test('works with Numerus instances', function (): void {
            expect(numerus(10)->not()->equals(numerus(5)))->toBeTrue();
            expect(numerus(10)->not()->equals(numerus(10)))->toBeFalse();
        });
    });

    describe('notEquals', function (): void {
        test('inverts not-equals check - returns true when equal', function (): void {
            expect(numerus(10)->not()->notEquals(10))->toBeTrue();
            expect(numerus(0)->not()->notEquals(0))->toBeTrue();
            expect(numerus(-5)->not()->notEquals(-5))->toBeTrue();
        });

        test('inverts not-equals check - returns false when not equal', function (): void {
            expect(numerus(10)->not()->notEquals(5))->toBeFalse();
            expect(numerus(10)->not()->notEquals(15))->toBeFalse();
            expect(numerus(-5)->not()->notEquals(5))->toBeFalse();
        });
    });

    describe('greaterThan', function (): void {
        test('inverts greater than check - returns true when less than or equal', function (): void {
            expect(numerus(10)->not()->greaterThan(20))->toBeTrue(); // 10 <= 20
            expect(numerus(10)->not()->greaterThan(10))->toBeTrue(); // 10 <= 10
            expect(numerus(5)->not()->greaterThan(10))->toBeTrue();  // 5 <= 10
        });

        test('inverts greater than check - returns false when greater', function (): void {
            expect(numerus(20)->not()->greaterThan(10))->toBeFalse(); // 20 > 10
            expect(numerus(15)->not()->greaterThan(5))->toBeFalse();  // 15 > 5
        });
    });

    describe('greaterThanOrEqual', function (): void {
        test('inverts greater than or equal check - returns true when less than', function (): void {
            expect(numerus(10)->not()->greaterThanOrEqual(20))->toBeTrue(); // 10 < 20
            expect(numerus(5)->not()->greaterThanOrEqual(10))->toBeTrue();  // 5 < 10
        });

        test('inverts greater than or equal check - returns false when greater or equal', function (): void {
            expect(numerus(10)->not()->greaterThanOrEqual(10))->toBeFalse(); // 10 >= 10
            expect(numerus(20)->not()->greaterThanOrEqual(10))->toBeFalse(); // 20 >= 10
        });
    });

    describe('lessThan', function (): void {
        test('inverts less than check - returns true when greater than or equal', function (): void {
            expect(numerus(20)->not()->lessThan(10))->toBeTrue(); // 20 >= 10
            expect(numerus(10)->not()->lessThan(10))->toBeTrue(); // 10 >= 10
            expect(numerus(15)->not()->lessThan(5))->toBeTrue();  // 15 >= 5
        });

        test('inverts less than check - returns false when less than', function (): void {
            expect(numerus(5)->not()->lessThan(10))->toBeFalse();  // 5 < 10
            expect(numerus(10)->not()->lessThan(20))->toBeFalse(); // 10 < 20
        });
    });

    describe('lessThanOrEqual', function (): void {
        test('inverts less than or equal check - returns true when greater than', function (): void {
            expect(numerus(20)->not()->lessThanOrEqual(10))->toBeTrue(); // 20 > 10
            expect(numerus(15)->not()->lessThanOrEqual(5))->toBeTrue();  // 15 > 5
        });

        test('inverts less than or equal check - returns false when less or equal', function (): void {
            expect(numerus(10)->not()->lessThanOrEqual(10))->toBeFalse(); // 10 <= 10
            expect(numerus(5)->not()->lessThanOrEqual(10))->toBeFalse();  // 5 <= 10
        });
    });

    describe('between', function (): void {
        test('inverts between check inclusive - returns false when within range', function (): void {
            expect(numerus(10)->not()->between(5, 15))->toBeFalse();
            expect(numerus(5)->not()->between(5, 15))->toBeFalse();  // boundary
            expect(numerus(15)->not()->between(5, 15))->toBeFalse(); // boundary
        });

        test('inverts between check inclusive - returns true when outside range', function (): void {
            expect(numerus(3)->not()->between(5, 15))->toBeTrue();
            expect(numerus(20)->not()->between(5, 15))->toBeTrue();
        });

        test('inverts between check exclusive - returns true when on boundary', function (): void {
            expect(numerus(5)->not()->between(5, 15, false))->toBeTrue();  // boundary excluded
            expect(numerus(15)->not()->between(5, 15, false))->toBeTrue(); // boundary excluded
        });

        test('inverts between check exclusive - returns false when strictly within', function (): void {
            expect(numerus(10)->not()->between(5, 15, false))->toBeFalse();
        });

        test('works with Numerus instances', function (): void {
            expect(numerus(10)->not()->between(numerus(5), numerus(15)))->toBeFalse();
            expect(numerus(20)->not()->between(numerus(5), numerus(15)))->toBeTrue();
        });
    });

    describe('notBetween', function (): void {
        test('inverts not-between check - returns true when within range', function (): void {
            expect(numerus(10)->not()->notBetween(5, 15))->toBeTrue();
            expect(numerus(5)->not()->notBetween(5, 15))->toBeTrue();  // boundary
            expect(numerus(15)->not()->notBetween(5, 15))->toBeTrue(); // boundary
        });

        test('inverts not-between check - returns false when outside range', function (): void {
            expect(numerus(3)->not()->notBetween(5, 15))->toBeFalse();
            expect(numerus(20)->not()->notBetween(5, 15))->toBeFalse();
        });

        test('inverts not-between check exclusive - returns false when on boundary', function (): void {
            expect(numerus(5)->not()->notBetween(5, 15, false))->toBeFalse();  // boundary excluded
            expect(numerus(15)->not()->notBetween(5, 15, false))->toBeFalse(); // boundary excluded
        });

        test('inverts not-between check exclusive - returns true when strictly within', function (): void {
            expect(numerus(10)->not()->notBetween(5, 15, false))->toBeTrue();
        });
    });

    describe('chaining with other operations', function (): void {
        test('can chain mathematical operations before negation', function (): void {
            expect(numerus(5)->plus(5)->not()->equals(15))->toBeTrue();
            expect(numerus(5)->multiplyBy(2)->not()->greaterThan(20))->toBeTrue();
        });

        test('maintains immutability', function (): void {
            $original = numerus(10);
            $negated = $original->not();

            expect($negated->equals(5))->toBeTrue();
            expect($original->equals(10))->toBeTrue(); // original unchanged
        });
    });
});
