<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Cline\Numerus\Numerus;

describe('Creation', function (): void {
    describe('Happy Path', function (): void {
        test('creates instance from integer', function (): void {
            $num = Numerus::create(42);
            expect($num->value())->toEqual(42);
        });

        test('creates instance from float', function (): void {
            $num = Numerus::create(42.5);
            expect($num->value())->toEqual(42.5);
        });

        test('creates instance from negative integer', function (): void {
            $num = Numerus::create(-42);
            expect($num->value())->toEqual(-42);
        });

        test('creates instance from zero', function (): void {
            $num = Numerus::create(0);
            expect($num->value())->toEqual(0);
        });

        test('creates instance from string in default locale', function (): void {
            $num = Numerus::create('1,234.56');
            expect($num->value())->toEqual(1_234.56);
        });

        test('creates instance from string in US locale', function (): void {
            $num = Numerus::create('1,234.56', 'en_US');
            expect($num->value())->toEqual(1_234.56);
        });

        test('creates instance from string in German locale', function (): void {
            $num = Numerus::create('1.234,56', 'de_DE');
            expect($num->value())->toEqual(1_234.56);
        });

        test('creates instance from string in French locale', function (): void {
            $num = Numerus::create('1 234,56', 'fr_FR');
            expect($num->value())->toEqual(1_234.56);
        });

        test('creates instance from negative string', function (): void {
            $num = Numerus::create('-1,234.56', 'en_US');
            expect($num->value())->toEqual(-1_234.56);
        });

        test('creates instance from string with currency code suffix', function (): void {
            $num = Numerus::create('1,347.55 EUR');
            expect($num->value())->toEqual(1_347.55);
        });

        test('creates instance from string with USD currency code', function (): void {
            $num = Numerus::create('1,234.56 USD', 'en_US');
            expect($num->value())->toEqual(1_234.56);
        });

        test('creates instance from German formatted string with EUR', function (): void {
            $num = Numerus::create('1.234,56 EUR', 'de_DE');
            expect($num->value())->toEqual(1_234.56);
        });
    });

    describe('Sad Path', function (): void {
        test('throws exception for invalid string', function (): void {
            Numerus::create('not a number');
        })->throws(InvalidArgumentException::class, "Unable to parse 'not a number' as float");
    });

    describe('Edge Cases', function (): void {
        test('creates instance from very large integer', function (): void {
            $num = Numerus::create(\PHP_INT_MAX);
            expect($num->value())->toEqual(\PHP_INT_MAX);
        });

        test('creates instance from very large negative integer', function (): void {
            $num = Numerus::create(\PHP_INT_MIN);
            expect($num->value())->toEqual(\PHP_INT_MIN);
        });

        test('creates instance from very small float', function (): void {
            $num = Numerus::create(0.000_000_1);
            expect($num->value())->toEqual(0.000_000_1);
        });

        test('creates instance from negative zero', function (): void {
            $num = Numerus::create(-0.0);
            expect($num->value())->toEqual(0.0);
        });

        test('creates instance from string with only decimals', function (): void {
            $num = Numerus::create('.56', 'en_US');
            expect($num->value())->toEqual(0.56);
        });

        test('creates instance from string with trailing zeros', function (): void {
            $num = Numerus::create('1,234.5600', 'en_US');
            expect($num->value())->toEqual(1_234.56);
        });

        test('creates instance from string with leading zeros', function (): void {
            $num = Numerus::create('00042', 'en_US');
            expect($num->value())->toEqual(42.0);
        });
    });
});

describe('Arithmetic Operations', function (): void {
    describe('Happy Path', function (): void {
        test('adds two numbers', function (): void {
            $num = Numerus::create(10);
            $result = $num->plus(5);

            expect($result->value())->toEqual(15);
            expect($num->value())->toEqual(10); // Original unchanged
        });

        test('adds with another Numerus instance', function (): void {
            $num1 = Numerus::create(10);
            $num2 = Numerus::create(5);
            $result = $num1->plus($num2);

            expect($result->value())->toEqual(15);
        });

        test('subtracts two numbers', function (): void {
            $num = Numerus::create(10);
            $result = $num->minus(3);

            expect($result->value())->toEqual(7);
            expect($num->value())->toEqual(10);
        });

        test('multiplies two numbers', function (): void {
            $num = Numerus::create(10);
            $result = $num->multiplyBy(3);

            expect($result->value())->toEqual(30);
            expect($num->value())->toEqual(10);
        });

        test('divides two numbers', function (): void {
            $num = Numerus::create(10);
            $result = $num->divideBy(2);

            expect($result->value())->toEqual(5);
            expect($num->value())->toEqual(10);
        });

        test('calculates modulo', function (): void {
            $num = Numerus::create(10);
            $result = $num->mod(3);

            expect($result->value())->toEqual(1);
        });

        test('raises to power', function (): void {
            $num = Numerus::create(2);
            $result = $num->power(3);

            expect($result->value())->toEqual(8);
        });

        test('calculates square root', function (): void {
            $num = Numerus::create(16);
            $result = $num->sqrt();

            expect($result->value())->toEqual(4.0);
        });
    });

    describe('Sad Path', function (): void {
        test('throws exception when dividing by zero', function (): void {
            $num = Numerus::create(10);
            $num->divideBy(0);
        })->throws(InvalidArgumentException::class, 'Division by zero');

        test('throws exception for modulo by zero', function (): void {
            $num = Numerus::create(10);
            $num->mod(0);
        })->throws(InvalidArgumentException::class, 'Modulo by zero');

        test('throws exception for square root of negative', function (): void {
            $num = Numerus::create(-16);
            $num->sqrt();
        })->throws(InvalidArgumentException::class, 'Cannot calculate square root of negative number');
    });

    describe('Edge Cases', function (): void {
        test('adds zero to number', function (): void {
            $num = Numerus::create(42);
            $result = $num->plus(0);

            expect($result->value())->toEqual(42);
        });

        test('subtracts zero from number', function (): void {
            $num = Numerus::create(42);
            $result = $num->minus(0);

            expect($result->value())->toEqual(42);
        });

        test('multiplies by zero', function (): void {
            $num = Numerus::create(42);
            $result = $num->multiplyBy(0);

            expect($result->value())->toEqual(0);
        });

        test('multiplies by one', function (): void {
            $num = Numerus::create(42);
            $result = $num->multiplyBy(1);

            expect($result->value())->toEqual(42);
        });

        test('divides by one', function (): void {
            $num = Numerus::create(42);
            $result = $num->divideBy(1);

            expect($result->value())->toEqual(42);
        });

        test('divides zero by number', function (): void {
            $num = Numerus::create(0);
            $result = $num->divideBy(5);

            expect($result->value())->toEqual(0);
        });

        test('calculates power of zero', function (): void {
            $num = Numerus::create(5);
            $result = $num->power(0);

            expect($result->value())->toEqual(1);
        });

        test('calculates power of one', function (): void {
            $num = Numerus::create(5);
            $result = $num->power(1);

            expect($result->value())->toEqual(5);
        });

        test('calculates zero to power', function (): void {
            $num = Numerus::create(0);
            $result = $num->power(5);

            expect($result->value())->toEqual(0);
        });

        test('calculates negative power', function (): void {
            $num = Numerus::create(2);
            $result = $num->power(-2);

            expect($result->value())->toEqual(0.25);
        });

        test('calculates square root of zero', function (): void {
            $num = Numerus::create(0);
            $result = $num->sqrt();

            expect($result->value())->toEqual(0.0);
        });

        test('calculates square root of one', function (): void {
            $num = Numerus::create(1);
            $result = $num->sqrt();

            expect($result->value())->toEqual(1.0);
        });

        test('adds very large numbers', function (): void {
            $num = Numerus::create(\PHP_INT_MAX - 1);
            $result = $num->plus(1);

            expect($result->value())->toEqual(\PHP_INT_MAX);
        });

        test('subtracts resulting in negative', function (): void {
            $num = Numerus::create(5);
            $result = $num->minus(10);

            expect($result->value())->toEqual(-5);
        });

        test('modulo with negative dividend', function (): void {
            $num = Numerus::create(-10);
            $result = $num->mod(3);

            expect($result->value())->toEqual(-1);
        });

        test('modulo with negative divisor', function (): void {
            $num = Numerus::create(10);
            $result = $num->mod(-3);

            expect($result->value())->toEqual(1);
        });
    });
});

describe('Rounding Operations', function (): void {
    describe('Happy Path', function (): void {
        test('returns absolute value', function (): void {
            $num = Numerus::create(-42);
            $result = $num->abs();

            expect($result->value())->toEqual(42);
        });

        test('rounds up with ceil', function (): void {
            $num = Numerus::create(42.3);
            $result = $num->ceil();

            expect($result->value())->toEqual(43.0);
        });

        test('rounds down with floor', function (): void {
            $num = Numerus::create(42.9);
            $result = $num->floor();

            expect($result->value())->toEqual(42.0);
        });

        test('rounds to nearest integer', function (): void {
            $num = Numerus::create(42.6);
            $result = $num->round();

            expect($result->value())->toEqual(43.0);
        });

        test('rounds with precision', function (): void {
            $num = Numerus::create(42.456);
            $result = $num->round(2);

            expect($result->value())->toEqual(42.46);
        });

        test('negates the number', function (): void {
            $num = Numerus::create(42);
            $result = $num->negate();

            expect($result->value())->toEqual(-42);
        });
    });

    describe('Edge Cases', function (): void {
        test('absolute value of zero', function (): void {
            $num = Numerus::create(0);
            $result = $num->abs();

            expect($result->value())->toEqual(0);
        });

        test('absolute value of positive number', function (): void {
            $num = Numerus::create(42);
            $result = $num->abs();

            expect($result->value())->toEqual(42);
        });

        test('ceil of integer', function (): void {
            $num = Numerus::create(42);
            $result = $num->ceil();

            expect($result->value())->toEqual(42.0);
        });

        test('ceil of negative number', function (): void {
            $num = Numerus::create(-42.3);
            $result = $num->ceil();

            expect($result->value())->toEqual(-42.0);
        });

        test('floor of integer', function (): void {
            $num = Numerus::create(42);
            $result = $num->floor();

            expect($result->value())->toEqual(42.0);
        });

        test('floor of negative number', function (): void {
            $num = Numerus::create(-42.9);
            $result = $num->floor();

            expect($result->value())->toEqual(-43.0);
        });

        test('rounds integer', function (): void {
            $num = Numerus::create(42);
            $result = $num->round();

            expect($result->value())->toEqual(42.0);
        });

        test('rounds exactly halfway up', function (): void {
            $num = Numerus::create(42.5);
            $result = $num->round();

            expect($result->value())->toEqual(43.0);
        });

        test('rounds exactly halfway down', function (): void {
            $num = Numerus::create(41.5);
            $result = $num->round();

            expect($result->value())->toEqual(42.0);
        });

        test('rounds negative number', function (): void {
            $num = Numerus::create(-42.6);
            $result = $num->round();

            expect($result->value())->toEqual(-43.0);
        });

        test('rounds with zero precision', function (): void {
            $num = Numerus::create(42.456);
            $result = $num->round(0);

            expect($result->value())->toEqual(42.0);
        });

        test('rounds with high precision', function (): void {
            $num = Numerus::create(42.123_456_789);
            $result = $num->round(5);

            expect($result->value())->toEqual(42.123_46);
        });

        test('negates zero', function (): void {
            $num = Numerus::create(0);
            $result = $num->negate();

            expect($result->value())->toEqual(0);
        });

        test('negates negative number', function (): void {
            $num = Numerus::create(-42);
            $result = $num->negate();

            expect($result->value())->toEqual(42);
        });
    });
});

describe('Comparison Operations', function (): void {
    describe('Happy Path', function (): void {
        test('checks equality', function (): void {
            $num = Numerus::create(42);

            expect($num->equals(42))->toBeTrue();
            expect($num->equals(43))->toBeFalse();
            expect($num->equals(Numerus::create(42)))->toBeTrue();
        });

        test('checks inequality', function (): void {
            $num = Numerus::create(42);

            expect($num->notEquals(43))->toBeTrue();
            expect($num->notEquals(42))->toBeFalse();
            expect($num->notEquals(Numerus::create(43)))->toBeTrue();
            expect($num->notEquals(Numerus::create(42)))->toBeFalse();
        });

        test('checks greater than', function (): void {
            $num = Numerus::create(42);

            expect($num->greaterThan(41))->toBeTrue();
            expect($num->greaterThan(42))->toBeFalse();
            expect($num->greaterThan(43))->toBeFalse();
        });

        test('checks greater than or equal', function (): void {
            $num = Numerus::create(42);

            expect($num->greaterThanOrEqual(41))->toBeTrue();
            expect($num->greaterThanOrEqual(42))->toBeTrue();
            expect($num->greaterThanOrEqual(43))->toBeFalse();
        });

        test('checks less than', function (): void {
            $num = Numerus::create(42);

            expect($num->lessThan(43))->toBeTrue();
            expect($num->lessThan(42))->toBeFalse();
            expect($num->lessThan(41))->toBeFalse();
        });

        test('checks less than or equal', function (): void {
            $num = Numerus::create(42);

            expect($num->lessThanOrEqual(43))->toBeTrue();
            expect($num->lessThanOrEqual(42))->toBeTrue();
            expect($num->lessThanOrEqual(41))->toBeFalse();
        });

        test('checks between inclusive', function (): void {
            $num = Numerus::create(50);

            expect($num->between(0, 100))->toBeTrue();
            expect($num->between(50, 100))->toBeTrue();
            expect($num->between(0, 50))->toBeTrue();
            expect($num->between(51, 100))->toBeFalse();
            expect($num->between(0, 49))->toBeFalse();
        });

        test('checks between exclusive', function (): void {
            $num = Numerus::create(50);

            expect($num->between(0, 100, false))->toBeTrue();
            expect($num->between(50, 100, false))->toBeFalse();
            expect($num->between(0, 50, false))->toBeFalse();
            expect($num->between(49, 51, false))->toBeTrue();
        });

        test('checks between with Numerus instances', function (): void {
            $num = Numerus::create(50);
            $min = Numerus::create(0);
            $max = Numerus::create(100);

            expect($num->between($min, $max))->toBeTrue();
            expect($num->between($min, $max, false))->toBeTrue();
        });

        test('checks not between inclusive', function (): void {
            $num = Numerus::create(50);

            expect($num->notBetween(0, 100))->toBeFalse();
            expect($num->notBetween(51, 100))->toBeTrue();
            expect($num->notBetween(0, 49))->toBeTrue();
        });

        test('checks not between exclusive', function (): void {
            $num = Numerus::create(50);

            expect($num->notBetween(0, 100, false))->toBeFalse();
            expect($num->notBetween(50, 100, false))->toBeTrue();
            expect($num->notBetween(0, 50, false))->toBeTrue();
        });
    });

    describe('Edge Cases', function (): void {
        test('compares zero with zero', function (): void {
            $num = Numerus::create(0);

            expect($num->equals(0))->toBeTrue();
            expect($num->equals(Numerus::create(0)))->toBeTrue();
        });

        test('compares negative numbers', function (): void {
            $num = Numerus::create(-42);

            expect($num->greaterThan(-43))->toBeTrue();
            expect($num->lessThan(-41))->toBeTrue();
        });

        test('compares float precision', function (): void {
            $num1 = Numerus::create(0.1);
            $num2 = Numerus::create(0.2);
            $result = $num1->plus($num2);

            expect($result->value())->toBeGreaterThan(0.29);
            expect($result->value())->toBeLessThan(0.31);
        });

        test('checks between with equal bounds', function (): void {
            $num = Numerus::create(50);

            expect($num->between(50, 50))->toBeTrue();
            expect($num->between(50, 50, false))->toBeFalse();
        });

        test('checks between with inverted bounds', function (): void {
            $num = Numerus::create(50);

            expect($num->between(100, 0))->toBeFalse();
        });
    });
});

describe('Min/Max Operations', function (): void {
    describe('Happy Path', function (): void {
        test('returns minimum value', function (): void {
            $num = Numerus::create(42);
            $result = $num->min(30);

            expect($result->value())->toEqual(30);
        });

        test('returns maximum value', function (): void {
            $num = Numerus::create(42);
            $result = $num->max(50);

            expect($result->value())->toEqual(50);
        });

        test('clamps value within range', function (): void {
            $num = Numerus::create(100);
            $result = $num->clamp(0, 50);

            expect($result->value())->toEqual(50);

            $num2 = Numerus::create(-10);
            $result2 = $num2->clamp(0, 50);

            expect($result2->value())->toEqual(0);

            $num3 = Numerus::create(25);
            $result3 = $num3->clamp(0, 50);

            expect($result3->value())->toEqual(25);
        });
    });

    describe('Sad Path', function (): void {
        test('throws exception when min is greater than max', function (): void {
            $num = Numerus::create(25);
            $num->clamp(50, 0);
        })->throws(InvalidArgumentException::class, 'Min value cannot be greater than max value');
    });

    describe('Edge Cases', function (): void {
        test('min with equal values', function (): void {
            $num = Numerus::create(42);
            $result = $num->min(42);

            expect($result->value())->toEqual(42);
        });

        test('min returns original when smaller', function (): void {
            $num = Numerus::create(30);
            $result = $num->min(42);

            expect($result->value())->toEqual(30);
        });

        test('max with equal values', function (): void {
            $num = Numerus::create(42);
            $result = $num->max(42);

            expect($result->value())->toEqual(42);
        });

        test('max returns original when larger', function (): void {
            $num = Numerus::create(50);
            $result = $num->max(42);

            expect($result->value())->toEqual(50);
        });

        test('clamps at exact min boundary', function (): void {
            $num = Numerus::create(0);
            $result = $num->clamp(0, 50);

            expect($result->value())->toEqual(0);
        });

        test('clamps at exact max boundary', function (): void {
            $num = Numerus::create(50);
            $result = $num->clamp(0, 50);

            expect($result->value())->toEqual(50);
        });

        test('clamps with equal min and max', function (): void {
            $num = Numerus::create(25);
            $result = $num->clamp(30, 30);

            expect($result->value())->toEqual(30);
        });

        test('clamps with negative range', function (): void {
            $num = Numerus::create(-100);
            $result = $num->clamp(-50, -10);

            expect($result->value())->toEqual(-50);
        });
    });
});

describe('Type Checking', function (): void {
    describe('Happy Path', function (): void {
        test('checks if positive', function (): void {
            expect(Numerus::create(42)->isPositive())->toBeTrue();
            expect(Numerus::create(0)->isPositive())->toBeFalse();
            expect(Numerus::create(-42)->isPositive())->toBeFalse();
        });

        test('checks if negative', function (): void {
            expect(Numerus::create(-42)->isNegative())->toBeTrue();
            expect(Numerus::create(0)->isNegative())->toBeFalse();
            expect(Numerus::create(42)->isNegative())->toBeFalse();
        });

        test('checks if zero', function (): void {
            expect(Numerus::create(0)->isZero())->toBeTrue();
            expect(Numerus::create(0.0)->isZero())->toBeTrue();
            expect(Numerus::create(42)->isZero())->toBeFalse();
        });

        test('checks if even', function (): void {
            expect(Numerus::create(42)->isEven())->toBeTrue();
            expect(Numerus::create(43)->isEven())->toBeFalse();
            expect(Numerus::create(0)->isEven())->toBeTrue();
        });

        test('checks if odd', function (): void {
            expect(Numerus::create(43)->isOdd())->toBeTrue();
            expect(Numerus::create(42)->isOdd())->toBeFalse();
            expect(Numerus::create(0)->isOdd())->toBeFalse();
        });

        test('checks if even returns false for floats', function (): void {
            expect(Numerus::create(42.5)->isEven())->toBeFalse();
            expect(Numerus::create(43.1)->isEven())->toBeFalse();
        });

        test('checks if odd returns false for floats', function (): void {
            expect(Numerus::create(42.5)->isOdd())->toBeFalse();
            expect(Numerus::create(43.1)->isOdd())->toBeFalse();
        });

        test('checks if integer', function (): void {
            expect(Numerus::create(42)->isInteger())->toBeTrue();
            expect(Numerus::create(42.0)->isInteger())->toBeTrue();
            expect(Numerus::create(42.5)->isInteger())->toBeFalse();
        });

        test('checks if float', function (): void {
            expect(Numerus::create(42.5)->isFloat())->toBeTrue();
            expect(Numerus::create(42)->isFloat())->toBeFalse();
            expect(Numerus::create(42.0)->isFloat())->toBeFalse();
        });

        test('returns sign of number', function (): void {
            expect(Numerus::create(42)->sign())->toEqual(1);
            expect(Numerus::create(-42)->sign())->toEqual(-1);
            expect(Numerus::create(0)->sign())->toEqual(0);
            expect(Numerus::create(0.0)->sign())->toEqual(0);
        });
    });

    describe('Edge Cases', function (): void {
        test('checks if very small positive number is positive', function (): void {
            expect(Numerus::create(0.000_000_1)->isPositive())->toBeTrue();
        });

        test('checks if very small negative number is negative', function (): void {
            expect(Numerus::create(-0.000_000_1)->isNegative())->toBeTrue();
        });

        test('checks negative zero is zero', function (): void {
            expect(Numerus::create(-0.0)->isZero())->toBeTrue();
        });

        test('checks if negative even number is even', function (): void {
            expect(Numerus::create(-42)->isEven())->toBeTrue();
        });

        test('checks if negative odd number is odd', function (): void {
            expect(Numerus::create(-43)->isOdd())->toBeTrue();
        });

        test('checks if very large integer is integer', function (): void {
            expect(Numerus::create(\PHP_INT_MAX)->isInteger())->toBeTrue();
        });

        test('checks if very small float is float', function (): void {
            expect(Numerus::create(0.000_000_1)->isFloat())->toBeTrue();
        });

        test('returns sign of very small positive number', function (): void {
            expect(Numerus::create(0.000_000_1)->sign())->toEqual(1);
        });

        test('returns sign of very small negative number', function (): void {
            expect(Numerus::create(-0.000_000_1)->sign())->toEqual(-1);
        });
    });
});

describe('Type Conversion', function (): void {
    describe('Happy Path', function (): void {
        test('converts to integer', function (): void {
            $num = Numerus::create(42.9);
            expect($num->toInt())->toEqual(42);
        });

        test('converts to float', function (): void {
            $num = Numerus::create(42);
            expect($num->toFloat())->toEqual(42.0);
        });

        test('converts to string', function (): void {
            $num = Numerus::create(42);
            expect($num->toString())->toBe('42');
            expect((string) $num)->toBe('42');
        });
    });

    describe('Edge Cases', function (): void {
        test('converts negative float to integer', function (): void {
            $num = Numerus::create(-42.9);
            expect($num->toInt())->toEqual(-42);
        });

        test('converts zero to integer', function (): void {
            $num = Numerus::create(0.0);
            expect($num->toInt())->toEqual(0);
        });

        test('converts integer to float preserves value', function (): void {
            $num = Numerus::create(42);
            expect($num->toFloat())->toEqual(42.0);
        });

        test('converts float to string with decimals', function (): void {
            $num = Numerus::create(42.5);
            expect($num->toString())->toBe('42.5');
        });

        test('converts zero to string', function (): void {
            $num = Numerus::create(0);
            expect($num->toString())->toBe('0');
        });

        test('converts negative to string', function (): void {
            $num = Numerus::create(-42);
            expect($num->toString())->toBe('-42');
        });
    });
});

describe('Percentage Operations', function (): void {
    describe('Happy Path', function (): void {
        test('calculates percentage of total', function (): void {
            expect(Numerus::create(25)->percentOf(100))->toEqual(25.0);
            expect(Numerus::create(50)->percentOf(200))->toEqual(25.0);
            expect(Numerus::create(75)->percentOf(150))->toEqual(50.0);
        });

        test('adds percentage to value', function (): void {
            expect(Numerus::create(100)->addPercent(20)->value())->toEqual(120.0);
            expect(Numerus::create(50)->addPercent(50)->value())->toEqual(75.0);
            expect(Numerus::create(100)->addPercent(0)->value())->toEqual(100);
        });

        test('subtracts percentage from value', function (): void {
            expect(Numerus::create(100)->subtractPercent(20)->value())->toEqual(80.0);
            expect(Numerus::create(50)->subtractPercent(50)->value())->toEqual(25.0);
            expect(Numerus::create(100)->subtractPercent(0)->value())->toEqual(100);
        });

        test('calculates percentage change', function (): void {
            expect(Numerus::create(50)->percentageChange(75))->toEqual(50.0);
            expect(Numerus::create(100)->percentageChange(80))->toEqual(-20.0);
            expect(Numerus::create(50)->percentageChange(50))->toEqual(0.0);
        });
    });

    describe('Sad Path', function (): void {
        test('throws exception for percentage of zero', function (): void {
            Numerus::create(25)->percentOf(0);
        })->throws(InvalidArgumentException::class, 'Cannot calculate percentage of zero');

        test('throws exception for percentage change from zero', function (): void {
            Numerus::create(0)->percentageChange(100);
        })->throws(InvalidArgumentException::class, 'Cannot calculate percentage change from zero');
    });

    describe('Edge Cases', function (): void {
        test('calculates percentage of same value', function (): void {
            expect(Numerus::create(100)->percentOf(100))->toEqual(100.0);
        });

        test('calculates percentage greater than total', function (): void {
            expect(Numerus::create(150)->percentOf(100))->toEqual(150.0);
        });

        test('calculates zero percentage of total', function (): void {
            expect(Numerus::create(0)->percentOf(100))->toEqual(0.0);
        });

        test('adds 100 percent doubles value', function (): void {
            expect(Numerus::create(50)->addPercent(100)->value())->toEqual(100);
        });

        test('subtracts 100 percent results in zero', function (): void {
            expect(Numerus::create(50)->subtractPercent(100)->value())->toEqual(0);
        });

        test('calculates percentage change to same value', function (): void {
            expect(Numerus::create(100)->percentageChange(100))->toEqual(0.0);
        });

        test('calculates percentage change to zero', function (): void {
            expect(Numerus::create(100)->percentageChange(0))->toEqual(-100.0);
        });

        test('calculates percentage change from negative', function (): void {
            expect(Numerus::create(-50)->percentageChange(-25))->toEqual(-50.0);
        });
    });
});

describe('Mathematical Operations', function (): void {
    describe('Happy Path', function (): void {
        test('calculates greatest common divisor', function (): void {
            expect(Numerus::create(12)->gcd(8)->value())->toEqual(4);
            expect(Numerus::create(54)->gcd(24)->value())->toEqual(6);
            expect(Numerus::create(17)->gcd(19)->value())->toEqual(1);
            expect(Numerus::create(100)->gcd(50)->value())->toEqual(50);
        });

        test('calculates gcd with negative numbers', function (): void {
            expect(Numerus::create(-12)->gcd(8)->value())->toEqual(4);
            expect(Numerus::create(12)->gcd(-8)->value())->toEqual(4);
        });

        test('calculates least common multiple', function (): void {
            expect(Numerus::create(4)->lcm(6)->value())->toEqual(12);
            expect(Numerus::create(12)->lcm(8)->value())->toEqual(24);
            expect(Numerus::create(5)->lcm(7)->value())->toEqual(35);
        });

        test('calculates factorial', function (): void {
            expect(Numerus::create(0)->factorial()->value())->toEqual(1);
            expect(Numerus::create(1)->factorial()->value())->toEqual(1);
            expect(Numerus::create(5)->factorial()->value())->toEqual(120);
            expect(Numerus::create(6)->factorial()->value())->toEqual(720);
        });
    });

    describe('Sad Path', function (): void {
        test('throws exception for lcm with zero', function (): void {
            Numerus::create(0)->lcm(5);
        })->throws(InvalidArgumentException::class, 'Cannot calculate LCM with zero');

        test('throws exception for negative factorial', function (): void {
            Numerus::create(-5)->factorial();
        })->throws(InvalidArgumentException::class, 'Cannot calculate factorial of negative number');

        test('throws exception for float factorial', function (): void {
            Numerus::create(5.5)->factorial();
        })->throws(InvalidArgumentException::class, 'Factorial requires an integer value');
    });

    describe('Edge Cases', function (): void {
        test('calculates gcd with same numbers', function (): void {
            expect(Numerus::create(12)->gcd(12)->value())->toEqual(12);
        });

        test('calculates gcd with one', function (): void {
            expect(Numerus::create(12)->gcd(1)->value())->toEqual(1);
        });

        test('calculates gcd with both negative', function (): void {
            expect(Numerus::create(-12)->gcd(-8)->value())->toEqual(4);
        });

        test('calculates lcm with same numbers', function (): void {
            expect(Numerus::create(12)->lcm(12)->value())->toEqual(12);
        });

        test('calculates lcm with one', function (): void {
            expect(Numerus::create(12)->lcm(1)->value())->toEqual(12);
        });

        test('calculates lcm with coprime numbers', function (): void {
            expect(Numerus::create(7)->lcm(13)->value())->toEqual(91);
        });

        test('calculates factorial of two', function (): void {
            expect(Numerus::create(2)->factorial()->value())->toEqual(2);
        });

        test('calculates factorial of three', function (): void {
            expect(Numerus::create(3)->factorial()->value())->toEqual(6);
        });

        test('calculates factorial of four', function (): void {
            expect(Numerus::create(4)->factorial()->value())->toEqual(24);
        });
    });
});

describe('Static Array Operations', function (): void {
    describe('Happy Path', function (): void {
        test('calculates average of array', function (): void {
            $avg = Numerus::average([10, 20, 30]);
            expect($avg->value())->toEqual(20);

            $avg = Numerus::average([1, 2, 3, 4, 5]);
            expect($avg->value())->toEqual(3);
        });

        test('calculates average with Numerus instances', function (): void {
            $avg = Numerus::average([
                Numerus::create(10),
                Numerus::create(20),
                Numerus::create(30),
            ]);
            expect($avg->value())->toEqual(20);
        });

        test('calculates sum of array', function (): void {
            $sum = Numerus::sum([10, 20, 30]);
            expect($sum->value())->toEqual(60);

            $sum = Numerus::sum([1, 2, 3, 4, 5]);
            expect($sum->value())->toEqual(15);
        });

        test('calculates sum with Numerus instances', function (): void {
            $sum = Numerus::sum([
                Numerus::create(10),
                Numerus::create(20),
                Numerus::create(30),
            ]);
            expect($sum->value())->toEqual(60);
        });

        test('calculates sum of empty array', function (): void {
            $sum = Numerus::sum([]);
            expect($sum->value())->toEqual(0);
        });
    });

    describe('Sad Path', function (): void {
        test('throws exception for average of empty array', function (): void {
            Numerus::average([]);
        })->throws(InvalidArgumentException::class, 'Cannot calculate average of empty array');
    });

    describe('Edge Cases', function (): void {
        test('calculates average of single element', function (): void {
            $avg = Numerus::average([42]);
            expect($avg->value())->toEqual(42);
        });

        test('calculates average with negative numbers', function (): void {
            $avg = Numerus::average([-10, 0, 10]);
            expect($avg->value())->toEqual(0);
        });

        test('calculates average with floats', function (): void {
            $avg = Numerus::average([1.5, 2.5, 3.5]);
            expect($avg->value())->toEqual(2.5);
        });

        test('calculates sum of single element', function (): void {
            $sum = Numerus::sum([42]);
            expect($sum->value())->toEqual(42);
        });

        test('calculates sum with negative numbers', function (): void {
            $sum = Numerus::sum([-10, 5, 15]);
            expect($sum->value())->toEqual(10);
        });

        test('calculates sum with floats', function (): void {
            $sum = Numerus::sum([1.5, 2.5, 3.5]);
            expect($sum->value())->toEqual(7.5);
        });

        test('calculates sum with all zeros', function (): void {
            $sum = Numerus::sum([0, 0, 0]);
            expect($sum->value())->toEqual(0);
        });
    });
});

describe('Number Parts', function (): void {
    describe('Happy Path', function (): void {
        test('extracts integer part from positive float', function (): void {
            expect(Numerus::create(12.34)->integerPart())->toEqual(12);
        });

        test('extracts fractional part from positive float', function (): void {
            $result = Numerus::create(12.34)->fractionalPart();
            expect($result)->toBeGreaterThan(0.33);
            expect($result)->toBeLessThan(0.35);
        });

        test('extracts integer part from negative float', function (): void {
            expect(Numerus::create(-12.34)->integerPart())->toEqual(-12);
        });

        test('extracts fractional part from negative float', function (): void {
            $result = Numerus::create(-12.34)->fractionalPart();
            expect($result)->toBeGreaterThan(0.33);
            expect($result)->toBeLessThan(0.35);
        });

        test('extracts integer part from integer', function (): void {
            expect(Numerus::create(42)->integerPart())->toEqual(42);
        });

        test('extracts fractional part from integer', function (): void {
            expect(Numerus::create(42)->fractionalPart())->toEqual(0.0);
        });
    });

    describe('Edge Cases', function (): void {
        test('extracts parts from zero', function (): void {
            expect(Numerus::create(0)->integerPart())->toEqual(0);
            expect(Numerus::create(0)->fractionalPart())->toEqual(0.0);
        });

        test('extracts parts from small positive decimal', function (): void {
            expect(Numerus::create(0.34)->integerPart())->toEqual(0);
            expect(Numerus::create(0.34)->fractionalPart())->toEqual(0.34);
        });

        test('extracts parts from small negative decimal', function (): void {
            expect(Numerus::create(-0.34)->integerPart())->toEqual(0);
            expect(Numerus::create(-0.34)->fractionalPart())->toEqual(0.34);
        });

        test('extracts parts from very large number', function (): void {
            $num = Numerus::create(9_999_999.99);
            expect($num->integerPart())->toEqual(9_999_999);
            expect($num->fractionalPart())->toBeGreaterThan(0.98);
            expect($num->fractionalPart())->toBeLessThan(1.0);
        });

        test('extracts parts from very small fractional part', function (): void {
            $num = Numerus::create(42.000_1);
            expect($num->integerPart())->toEqual(42);
            expect($num->fractionalPart())->toBeGreaterThan(0.0);
            expect($num->fractionalPart())->toBeLessThan(0.001);
        });
    });
});

describe('Immutability', function (): void {
    test('preserves original value after operations', function (): void {
        $original = Numerus::create(10);

        $original->plus(5);
        $original->minus(3);
        $original->multiplyBy(2);
        $original->divideBy(2);

        expect($original->value())->toEqual(10);
    });

    test('chains operations correctly', function (): void {
        $result = Numerus::create(10)
            ->plus(5)
            ->minus(3)
            ->multiplyBy(2)
            ->divideBy(4);

        expect($result->value())->toEqual(6);
    });

    test('chains multiple rounding operations', function (): void {
        $result = Numerus::create(42.789)
            ->round(2)
            ->plus(0.01)
            ->floor();

        expect($result->value())->toEqual(42.0);
    });

    test('chains comparison and arithmetic operations', function (): void {
        $num = Numerus::create(10);
        $doubled = $num->multiplyBy(2);

        expect($num->value())->toEqual(10);
        expect($doubled->value())->toEqual(20);
        expect($num->equals(10))->toBeTrue();
    });
});
