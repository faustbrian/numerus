<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Cline\Numerus\Numerus;
use Cline\Numerus\Percentage;

describe('Percentage Of', function (): void {
    describe('Happy Path', function (): void {
        test('calculates percentage of total', function (): void {
            expect(Percentage::of(25, 100))->toBe(25.0);
            expect(Percentage::of(50, 200))->toBe(25.0);
            expect(Percentage::of(75, 150))->toBe(50.0);
        });

        test('works with Numerus instances', function (): void {
            expect(Percentage::of(Numerus::create(25), Numerus::create(100)))->toBe(25.0);
            expect(Percentage::of(Numerus::create(50), 200))->toBe(25.0);
            expect(Percentage::of(75, Numerus::create(150)))->toBe(50.0);
        });
    });

    describe('Sad Path', function (): void {
        test('throws exception for zero total', function (): void {
            Percentage::of(25, 0);
        })->throws(InvalidArgumentException::class, 'Cannot calculate percentage with zero total');

        test('throws exception for zero float total', function (): void {
            Percentage::of(25, 0.0);
        })->throws(InvalidArgumentException::class, 'Cannot calculate percentage with zero total');
    });

    describe('Edge Cases', function (): void {
        test('calculates 100 percent', function (): void {
            expect(Percentage::of(100, 100))->toBe(100.0);
        });

        test('calculates 0 percent', function (): void {
            expect(Percentage::of(0, 100))->toBe(0.0);
        });

        test('calculates percentage greater than 100', function (): void {
            expect(Percentage::of(150, 100))->toBe(150.0);
        });

        test('calculates percentage with negative value', function (): void {
            expect(Percentage::of(-25, 100))->toBe(-25.0);
        });

        test('calculates percentage with negative total', function (): void {
            expect(Percentage::of(25, -100))->toBe(-25.0);
        });

        test('calculates percentage with both negative', function (): void {
            expect(Percentage::of(-25, -100))->toBe(25.0);
        });

        test('calculates very small percentage', function (): void {
            expect(Percentage::of(1, 1_000))->toBe(0.1);
        });

        test('calculates fractional percentage', function (): void {
            expect(Percentage::of(33.33, 100))->toBe(33.33);
        });
    });
});

describe('Percentage Difference', function (): void {
    describe('Happy Path', function (): void {
        test('calculates percentage increase', function (): void {
            expect(Percentage::differenceBetween(50, 75))->toBe(50.0);
            expect(Percentage::differenceBetween(100, 150))->toBe(50.0);
        });

        test('calculates percentage decrease', function (): void {
            expect(Percentage::differenceBetween(100, 80))->toBe(-20.0);
            expect(Percentage::differenceBetween(50, 25))->toBe(-50.0);
        });

        test('works with Numerus instances', function (): void {
            expect(Percentage::differenceBetween(Numerus::create(50), Numerus::create(75)))->toBe(50.0);
            expect(Percentage::differenceBetween(Numerus::create(100), 80))->toBe(-20.0);
            expect(Percentage::differenceBetween(50, Numerus::create(25)))->toBe(-50.0);
        });
    });

    describe('Sad Path', function (): void {
        test('throws exception for zero original', function (): void {
            Percentage::differenceBetween(0, 100);
        })->throws(InvalidArgumentException::class, 'Cannot calculate percentage difference from zero');

        test('throws exception for zero float original', function (): void {
            Percentage::differenceBetween(0.0, 100);
        })->throws(InvalidArgumentException::class, 'Cannot calculate percentage difference from zero');
    });

    describe('Edge Cases', function (): void {
        test('calculates no change', function (): void {
            expect(Percentage::differenceBetween(100, 100))->toBe(0.0);
        });

        test('calculates 100 percent increase', function (): void {
            expect(Percentage::differenceBetween(50, 100))->toBe(100.0);
        });

        test('calculates 100 percent decrease', function (): void {
            expect(Percentage::differenceBetween(100, 0))->toBe(-100.0);
        });

        test('calculates with negative original', function (): void {
            expect(Percentage::differenceBetween(-50, -75))->toBe(50.0);
            expect(Percentage::differenceBetween(-100, -50))->toBe(-50.0);
        });

        test('calculates from negative to positive', function (): void {
            expect(Percentage::differenceBetween(-50, 50))->toBe(-200.0);
        });

        test('calculates very small change', function (): void {
            expect(Percentage::differenceBetween(1_000, 1_001))->toBe(0.1);
        });
    });
});

describe('Absolute Percentage Difference', function (): void {
    describe('Happy Path', function (): void {
        test('calculates absolute difference', function (): void {
            expect(Percentage::absoluteDifferenceBetween(50, 75))->toBe(50.0);
            expect(Percentage::absoluteDifferenceBetween(100, 80))->toBe(20.0);
            expect(Percentage::absoluteDifferenceBetween(100, 150))->toBe(50.0);
        });

        test('works with Numerus instances', function (): void {
            expect(Percentage::absoluteDifferenceBetween(Numerus::create(100), Numerus::create(80)))->toBe(20.0);
            expect(Percentage::absoluteDifferenceBetween(Numerus::create(50), 75))->toBe(50.0);
            expect(Percentage::absoluteDifferenceBetween(100, Numerus::create(150)))->toBe(50.0);
        });
    });

    describe('Edge Cases', function (): void {
        test('calculates no difference', function (): void {
            expect(Percentage::absoluteDifferenceBetween(100, 100))->toBe(0.0);
        });

        test('returns positive for increase', function (): void {
            expect(Percentage::absoluteDifferenceBetween(50, 75))->toBe(50.0);
        });

        test('returns positive for decrease', function (): void {
            expect(Percentage::absoluteDifferenceBetween(75, 50))->toBe(33.333_333_333_333_33);
        });

        test('calculates with negative numbers', function (): void {
            expect(Percentage::absoluteDifferenceBetween(-50, -75))->toBe(50.0);
        });

        test('calculates complete change', function (): void {
            expect(Percentage::absoluteDifferenceBetween(100, 0))->toBe(100.0);
        });
    });
});

describe('Calculate Percentage', function (): void {
    describe('Happy Path', function (): void {
        test('calculates percentage of number', function (): void {
            expect(Percentage::calculate(20, 100))->toBe(20.0);
            expect(Percentage::calculate(50, 200))->toBe(100.0);
            expect(Percentage::calculate(10, 50))->toBe(5.0);
        });

        test('works with Numerus instances', function (): void {
            expect(Percentage::calculate(20, Numerus::create(100)))->toBe(20.0);
            expect(Percentage::calculate(50, Numerus::create(200)))->toBe(100.0);
        });
    });

    describe('Edge Cases', function (): void {
        test('calculates zero percentage', function (): void {
            expect(Percentage::calculate(0, 100))->toBe(0.0);
        });

        test('calculates percentage of zero', function (): void {
            expect(Percentage::calculate(50, 0))->toBe(0.0);
        });

        test('calculates 100 percent', function (): void {
            expect(Percentage::calculate(100, 100))->toBe(100.0);
        });

        test('calculates with negative percentage', function (): void {
            expect(Percentage::calculate(-20, 100))->toBe(-20.0);
        });

        test('calculates with negative number', function (): void {
            expect(Percentage::calculate(20, -100))->toBe(-20.0);
        });

        test('calculates fractional percentage', function (): void {
            expect(Percentage::calculate(33.33, 100))->toBe(33.33);
        });

        test('calculates very small percentage', function (): void {
            expect(Percentage::calculate(0.1, 1_000))->toBe(1.0);
        });
    });
});

describe('Add Percentage', function (): void {
    describe('Happy Path', function (): void {
        test('adds percentage to number', function (): void {
            expect(Percentage::add(20, 100))->toBe(120.0);
            expect(Percentage::add(50, 100))->toBe(150.0);
            expect(Percentage::add(10, 50))->toBe(55.0);
        });

        test('works with Numerus instances', function (): void {
            expect(Percentage::add(20, Numerus::create(100)))->toBe(120.0);
            expect(Percentage::add(50, Numerus::create(100)))->toBe(150.0);
        });
    });

    describe('Edge Cases', function (): void {
        test('adds zero percentage', function (): void {
            expect(Percentage::add(0, 100))->toBe(100.0);
        });

        test('adds percentage to zero', function (): void {
            expect(Percentage::add(50, 0))->toBe(0.0);
        });

        test('adds 100 percent doubles value', function (): void {
            expect(Percentage::add(100, 50))->toBe(100.0);
        });

        test('adds with negative percentage', function (): void {
            expect(Percentage::add(-20, 100))->toBe(80.0);
        });

        test('adds to negative number', function (): void {
            expect(Percentage::add(20, -100))->toBe(-120.0);
        });

        test('adds fractional percentage', function (): void {
            expect(Percentage::add(10.5, 100))->toBe(110.5);
        });

        test('adds very small percentage', function (): void {
            expect(Percentage::add(0.01, 100))->toBe(100.01);
        });
    });
});

describe('Subtract Percentage', function (): void {
    describe('Happy Path', function (): void {
        test('subtracts percentage from number', function (): void {
            expect(Percentage::subtract(20, 100))->toBe(80.0);
            expect(Percentage::subtract(50, 100))->toBe(50.0);
            expect(Percentage::subtract(10, 50))->toBe(45.0);
        });

        test('works with Numerus instances', function (): void {
            expect(Percentage::subtract(20, Numerus::create(100)))->toBe(80.0);
            expect(Percentage::subtract(50, Numerus::create(100)))->toBe(50.0);
        });
    });

    describe('Edge Cases', function (): void {
        test('subtracts zero percentage', function (): void {
            expect(Percentage::subtract(0, 100))->toBe(100.0);
        });

        test('subtracts from zero', function (): void {
            expect(Percentage::subtract(50, 0))->toBe(0.0);
        });

        test('subtracts 100 percent results in zero', function (): void {
            expect(Percentage::subtract(100, 100))->toBe(0.0);
        });

        test('subtracts with negative percentage', function (): void {
            expect(Percentage::subtract(-20, 100))->toBe(120.0);
        });

        test('subtracts from negative number', function (): void {
            expect(Percentage::subtract(20, -100))->toBe(-80.0);
        });

        test('subtracts fractional percentage', function (): void {
            expect(Percentage::subtract(10.5, 100))->toBe(89.5);
        });

        test('subtracts very small percentage', function (): void {
            expect(Percentage::subtract(0.01, 100))->toBe(99.99);
        });

        test('subtracts more than 100 percent results in negative', function (): void {
            expect(Percentage::subtract(150, 100))->toBe(-50.0);
        });
    });
});
