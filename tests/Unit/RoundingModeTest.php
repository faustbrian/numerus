<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Cline\Numerus\Numerus;

describe('RoundingMode', function (): void {
    describe('AwayFromZero', function (): void {
        describe('Happy Path', function (): void {
            test('rounds away from zero for positive numbers', function (): void {
                expect(Numerus::create(2.1)->round(0, RoundingMode::AwayFromZero)->value())->toBe(3.0);
                expect(Numerus::create(2.9)->round(0, RoundingMode::AwayFromZero)->value())->toBe(3.0);
            });

            test('rounds away from zero for negative numbers', function (): void {
                expect(Numerus::create(-2.1)->round(0, RoundingMode::AwayFromZero)->value())->toBe(-3.0);
                expect(Numerus::create(-2.9)->round(0, RoundingMode::AwayFromZero)->value())->toBe(-3.0);
            });

            test('rounds with precision', function (): void {
                expect(Numerus::create(2.15)->round(1, RoundingMode::AwayFromZero)->value())->toBe(2.2);
            });
        });

        describe('Edge Cases', function (): void {
            test('rounds zero', function (): void {
                expect(Numerus::create(0)->round(0, RoundingMode::AwayFromZero)->value())->toBe(0.0);
            });

            test('rounds exactly halfway', function (): void {
                expect(Numerus::create(2.5)->round(0, RoundingMode::AwayFromZero)->value())->toBe(3.0);
                expect(Numerus::create(-2.5)->round(0, RoundingMode::AwayFromZero)->value())->toBe(-3.0);
            });

            test('rounds integer unchanged', function (): void {
                expect(Numerus::create(5)->round(0, RoundingMode::AwayFromZero)->value())->toBe(5.0);
            });
        });
    });

    describe('TowardsZero', function (): void {
        describe('Happy Path', function (): void {
            test('rounds towards zero for positive numbers', function (): void {
                expect(Numerus::create(2.1)->round(0, RoundingMode::TowardsZero)->value())->toBe(2.0);
                expect(Numerus::create(2.9)->round(0, RoundingMode::TowardsZero)->value())->toBe(2.0);
            });

            test('rounds towards zero for negative numbers', function (): void {
                expect(Numerus::create(-2.1)->round(0, RoundingMode::TowardsZero)->value())->toBe(-2.0);
                expect(Numerus::create(-2.9)->round(0, RoundingMode::TowardsZero)->value())->toBe(-2.0);
            });

            test('rounds with precision', function (): void {
                expect(Numerus::create(2.19)->round(1, RoundingMode::TowardsZero)->value())->toBe(2.1);
            });
        });

        describe('Edge Cases', function (): void {
            test('rounds zero', function (): void {
                expect(Numerus::create(0)->round(0, RoundingMode::TowardsZero)->value())->toBe(0.0);
            });

            test('rounds exactly halfway', function (): void {
                expect(Numerus::create(2.5)->round(0, RoundingMode::TowardsZero)->value())->toBe(2.0);
                expect(Numerus::create(-2.5)->round(0, RoundingMode::TowardsZero)->value())->toBe(-2.0);
            });

            test('rounds integer unchanged', function (): void {
                expect(Numerus::create(5)->round(0, RoundingMode::TowardsZero)->value())->toBe(5.0);
            });
        });
    });

    describe('PositiveInfinity', function (): void {
        describe('Happy Path', function (): void {
            test('rounds towards positive infinity for positive numbers', function (): void {
                expect(Numerus::create(2.1)->round(0, RoundingMode::PositiveInfinity)->value())->toBe(3.0);
                expect(Numerus::create(2.9)->round(0, RoundingMode::PositiveInfinity)->value())->toBe(3.0);
            });

            test('rounds towards positive infinity for negative numbers', function (): void {
                expect(Numerus::create(-2.1)->round(0, RoundingMode::PositiveInfinity)->value())->toBe(-2.0);
                expect(Numerus::create(-2.9)->round(0, RoundingMode::PositiveInfinity)->value())->toBe(-2.0);
            });

            test('rounds with precision', function (): void {
                expect(Numerus::create(2.11)->round(1, RoundingMode::PositiveInfinity)->value())->toBe(2.2);
            });
        });

        describe('Edge Cases', function (): void {
            test('rounds zero', function (): void {
                expect(Numerus::create(0)->round(0, RoundingMode::PositiveInfinity)->value())->toBe(0.0);
            });

            test('rounds exactly halfway', function (): void {
                expect(Numerus::create(2.5)->round(0, RoundingMode::PositiveInfinity)->value())->toBe(3.0);
                expect(Numerus::create(-2.5)->round(0, RoundingMode::PositiveInfinity)->value())->toBe(-2.0);
            });

            test('rounds integer unchanged', function (): void {
                expect(Numerus::create(5)->round(0, RoundingMode::PositiveInfinity)->value())->toBe(5.0);
            });
        });
    });

    describe('NegativeInfinity', function (): void {
        describe('Happy Path', function (): void {
            test('rounds towards negative infinity for positive numbers', function (): void {
                expect(Numerus::create(2.1)->round(0, RoundingMode::NegativeInfinity)->value())->toBe(2.0);
                expect(Numerus::create(2.9)->round(0, RoundingMode::NegativeInfinity)->value())->toBe(2.0);
            });

            test('rounds towards negative infinity for negative numbers', function (): void {
                expect(Numerus::create(-2.1)->round(0, RoundingMode::NegativeInfinity)->value())->toBe(-3.0);
                expect(Numerus::create(-2.9)->round(0, RoundingMode::NegativeInfinity)->value())->toBe(-3.0);
            });

            test('rounds with precision', function (): void {
                expect(Numerus::create(2.19)->round(1, RoundingMode::NegativeInfinity)->value())->toBe(2.1);
            });
        });

        describe('Edge Cases', function (): void {
            test('rounds zero', function (): void {
                expect(Numerus::create(0)->round(0, RoundingMode::NegativeInfinity)->value())->toBe(0.0);
            });

            test('rounds exactly halfway', function (): void {
                expect(Numerus::create(2.5)->round(0, RoundingMode::NegativeInfinity)->value())->toBe(2.0);
                expect(Numerus::create(-2.5)->round(0, RoundingMode::NegativeInfinity)->value())->toBe(-3.0);
            });

            test('rounds integer unchanged', function (): void {
                expect(Numerus::create(5)->round(0, RoundingMode::NegativeInfinity)->value())->toBe(5.0);
            });
        });
    });

    describe('HalfAwayFromZero', function (): void {
        describe('Happy Path', function (): void {
            test('rounds to nearest neighbor', function (): void {
                expect(Numerus::create(2.4)->round(0, RoundingMode::HalfAwayFromZero)->value())->toBe(2.0);
                expect(Numerus::create(2.6)->round(0, RoundingMode::HalfAwayFromZero)->value())->toBe(3.0);
            });

            test('rounds ties away from zero', function (): void {
                expect(Numerus::create(2.5)->round(0, RoundingMode::HalfAwayFromZero)->value())->toBe(3.0);
                expect(Numerus::create(-2.5)->round(0, RoundingMode::HalfAwayFromZero)->value())->toBe(-3.0);
            });

            test('rounds with precision', function (): void {
                expect(Numerus::create(2.15)->round(1, RoundingMode::HalfAwayFromZero)->value())->toBe(2.2);
            });
        });

        describe('Edge Cases', function (): void {
            test('rounds zero', function (): void {
                expect(Numerus::create(0)->round(0, RoundingMode::HalfAwayFromZero)->value())->toBe(0.0);
            });

            test('rounds integer unchanged', function (): void {
                expect(Numerus::create(5)->round(0, RoundingMode::HalfAwayFromZero)->value())->toBe(5.0);
            });

            test('rounds 1.5 and 3.5', function (): void {
                expect(Numerus::create(1.5)->round(0, RoundingMode::HalfAwayFromZero)->value())->toBe(2.0);
                expect(Numerus::create(3.5)->round(0, RoundingMode::HalfAwayFromZero)->value())->toBe(4.0);
            });
        });
    });

    describe('HalfTowardsZero', function (): void {
        describe('Happy Path', function (): void {
            test('rounds to nearest neighbor', function (): void {
                expect(Numerus::create(2.4)->round(0, RoundingMode::HalfTowardsZero)->value())->toBe(2.0);
                expect(Numerus::create(2.6)->round(0, RoundingMode::HalfTowardsZero)->value())->toBe(3.0);
            });

            test('rounds ties towards zero', function (): void {
                expect(Numerus::create(2.5)->round(0, RoundingMode::HalfTowardsZero)->value())->toBe(2.0);
                expect(Numerus::create(-2.5)->round(0, RoundingMode::HalfTowardsZero)->value())->toBe(-2.0);
            });

            test('rounds with precision', function (): void {
                expect(Numerus::create(2.15)->round(1, RoundingMode::HalfTowardsZero)->value())->toBe(2.1);
            });
        });

        describe('Edge Cases', function (): void {
            test('rounds zero', function (): void {
                expect(Numerus::create(0)->round(0, RoundingMode::HalfTowardsZero)->value())->toBe(0.0);
            });

            test('rounds integer unchanged', function (): void {
                expect(Numerus::create(5)->round(0, RoundingMode::HalfTowardsZero)->value())->toBe(5.0);
            });

            test('rounds 1.5 and 3.5', function (): void {
                expect(Numerus::create(1.5)->round(0, RoundingMode::HalfTowardsZero)->value())->toBe(1.0);
                expect(Numerus::create(3.5)->round(0, RoundingMode::HalfTowardsZero)->value())->toBe(3.0);
            });
        });
    });

    describe('HalfEven (Bankers Rounding)', function (): void {
        describe('Happy Path', function (): void {
            test('rounds to nearest neighbor', function (): void {
                expect(Numerus::create(2.4)->round(0, RoundingMode::HalfEven)->value())->toBe(2.0);
                expect(Numerus::create(2.6)->round(0, RoundingMode::HalfEven)->value())->toBe(3.0);
            });

            test('rounds ties to even', function (): void {
                expect(Numerus::create(2.5)->round(0, RoundingMode::HalfEven)->value())->toBe(2.0);
                expect(Numerus::create(3.5)->round(0, RoundingMode::HalfEven)->value())->toBe(4.0);
                expect(Numerus::create(4.5)->round(0, RoundingMode::HalfEven)->value())->toBe(4.0);
                expect(Numerus::create(5.5)->round(0, RoundingMode::HalfEven)->value())->toBe(6.0);
            });

            test('rounds with precision', function (): void {
                expect(Numerus::create(2.15)->round(1, RoundingMode::HalfEven)->value())->toBe(2.2);
                expect(Numerus::create(2.25)->round(1, RoundingMode::HalfEven)->value())->toBe(2.2);
            });
        });

        describe('Edge Cases', function (): void {
            test('rounds zero', function (): void {
                expect(Numerus::create(0)->round(0, RoundingMode::HalfEven)->value())->toBe(0.0);
            });

            test('rounds integer unchanged', function (): void {
                expect(Numerus::create(5)->round(0, RoundingMode::HalfEven)->value())->toBe(5.0);
            });

            test('rounds negative ties to even', function (): void {
                expect(Numerus::create(-2.5)->round(0, RoundingMode::HalfEven)->value())->toBe(-2.0);
                expect(Numerus::create(-3.5)->round(0, RoundingMode::HalfEven)->value())->toBe(-4.0);
            });

            test('rounds 0.5 and 1.5', function (): void {
                expect(Numerus::create(0.5)->round(0, RoundingMode::HalfEven)->value())->toBe(0.0);
                expect(Numerus::create(1.5)->round(0, RoundingMode::HalfEven)->value())->toBe(2.0);
            });
        });
    });

    describe('HalfOdd', function (): void {
        describe('Happy Path', function (): void {
            test('rounds to nearest neighbor', function (): void {
                expect(Numerus::create(2.4)->round(0, RoundingMode::HalfOdd)->value())->toBe(2.0);
                expect(Numerus::create(2.6)->round(0, RoundingMode::HalfOdd)->value())->toBe(3.0);
            });

            test('rounds ties to odd', function (): void {
                expect(Numerus::create(2.5)->round(0, RoundingMode::HalfOdd)->value())->toBe(3.0);
                expect(Numerus::create(3.5)->round(0, RoundingMode::HalfOdd)->value())->toBe(3.0);
                expect(Numerus::create(4.5)->round(0, RoundingMode::HalfOdd)->value())->toBe(5.0);
                expect(Numerus::create(5.5)->round(0, RoundingMode::HalfOdd)->value())->toBe(5.0);
            });
        });

        describe('Edge Cases', function (): void {
            test('rounds zero', function (): void {
                expect(Numerus::create(0)->round(0, RoundingMode::HalfOdd)->value())->toBe(0.0);
            });

            test('rounds integer unchanged', function (): void {
                expect(Numerus::create(5)->round(0, RoundingMode::HalfOdd)->value())->toBe(5.0);
            });

            test('rounds negative ties to odd', function (): void {
                expect(Numerus::create(-2.5)->round(0, RoundingMode::HalfOdd)->value())->toBe(-3.0);
                expect(Numerus::create(-3.5)->round(0, RoundingMode::HalfOdd)->value())->toBe(-3.0);
            });

            test('rounds 0.5 and 1.5', function (): void {
                expect(Numerus::create(0.5)->round(0, RoundingMode::HalfOdd)->value())->toBe(1.0);
                expect(Numerus::create(1.5)->round(0, RoundingMode::HalfOdd)->value())->toBe(1.0);
            });
        });
    });

    describe('Default Mode', function (): void {
        test('defaults to HalfAwayFromZero when no mode specified', function (): void {
            expect(Numerus::create(2.5)->round()->value())->toBe(3.0);
            expect(Numerus::create(2.4)->round()->value())->toBe(2.0);
        });
    });

    describe('Immutability', function (): void {
        test('maintains immutability', function (): void {
            $original = Numerus::create(2.5);
            $rounded = $original->round(0, RoundingMode::AwayFromZero);

            expect($original->value())->toBe(2.5);
            expect($rounded->value())->toBe(3.0);
        });
    });

    describe('Convenience Methods', function (): void {
        describe('Happy Path', function (): void {
            test('roundAwayFromZero convenience method', function (): void {
                expect(Numerus::create(2.1)->roundAwayFromZero()->value())->toBe(3.0);
                expect(Numerus::create(-2.1)->roundAwayFromZero()->value())->toBe(-3.0);
            });

            test('roundTowardsZero convenience method', function (): void {
                expect(Numerus::create(2.9)->roundTowardsZero()->value())->toBe(2.0);
                expect(Numerus::create(-2.9)->roundTowardsZero()->value())->toBe(-2.0);
            });

            test('roundPositiveInfinity convenience method', function (): void {
                expect(Numerus::create(2.1)->roundPositiveInfinity()->value())->toBe(3.0);
                expect(Numerus::create(-2.9)->roundPositiveInfinity()->value())->toBe(-2.0);
            });

            test('roundNegativeInfinity convenience method', function (): void {
                expect(Numerus::create(2.9)->roundNegativeInfinity()->value())->toBe(2.0);
                expect(Numerus::create(-2.1)->roundNegativeInfinity()->value())->toBe(-3.0);
            });

            test('roundHalfAwayFromZero convenience method', function (): void {
                expect(Numerus::create(2.5)->roundHalfAwayFromZero()->value())->toBe(3.0);
                expect(Numerus::create(-2.5)->roundHalfAwayFromZero()->value())->toBe(-3.0);
            });

            test('roundHalfTowardsZero convenience method', function (): void {
                expect(Numerus::create(2.5)->roundHalfTowardsZero()->value())->toBe(2.0);
                expect(Numerus::create(-2.5)->roundHalfTowardsZero()->value())->toBe(-2.0);
            });

            test('roundHalfEven convenience method', function (): void {
                expect(Numerus::create(2.5)->roundHalfEven()->value())->toBe(2.0);
                expect(Numerus::create(3.5)->roundHalfEven()->value())->toBe(4.0);
            });

            test('roundHalfOdd convenience method', function (): void {
                expect(Numerus::create(2.5)->roundHalfOdd()->value())->toBe(3.0);
                expect(Numerus::create(3.5)->roundHalfOdd()->value())->toBe(3.0);
            });
        });

        describe('Edge Cases', function (): void {
            test('convenience methods handle zero', function (): void {
                expect(Numerus::create(0)->roundAwayFromZero()->value())->toBe(0.0);
                expect(Numerus::create(0)->roundTowardsZero()->value())->toBe(0.0);
                expect(Numerus::create(0)->roundHalfEven()->value())->toBe(0.0);
            });

            test('convenience methods handle integers', function (): void {
                expect(Numerus::create(5)->roundAwayFromZero()->value())->toBe(5.0);
                expect(Numerus::create(5)->roundTowardsZero()->value())->toBe(5.0);
                expect(Numerus::create(5)->roundHalfEven()->value())->toBe(5.0);
            });

            test('convenience methods preserve immutability', function (): void {
                $original = Numerus::create(2.5);
                $rounded = $original->roundHalfEven();

                expect($original->value())->toBe(2.5);
                expect($rounded->value())->toBe(2.0);
            });
        });
    });
});
