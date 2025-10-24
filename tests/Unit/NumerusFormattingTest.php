<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Cline\Numerus\Numerus;

describe('Abbreviation Formatting', function (): void {
    describe('Happy Path', function (): void {
        test('abbreviates thousands', function (): void {
            expect(Numerus::create(1_000)->abbreviate())->toBe('1K');
            expect(Numerus::create(1_500)->abbreviate())->toBe('2K');
            expect(Numerus::create(999)->abbreviate())->toBe('999');
        });

        test('abbreviates millions', function (): void {
            expect(Numerus::create(1_000_000)->abbreviate())->toBe('1M');
            expect(Numerus::create(1_500_000)->abbreviate())->toBe('2M');
            expect(Numerus::create(1_230_000)->abbreviate(2))->toBe('1.23M');
        });

        test('abbreviates billions', function (): void {
            expect(Numerus::create(1_000_000_000)->abbreviate())->toBe('1B');
            expect(Numerus::create(1_500_000_000)->abbreviate(1))->toBe('1.5B');
        });

        test('abbreviates trillions', function (): void {
            expect(Numerus::create(1_000_000_000_000)->abbreviate())->toBe('1T');
            expect(Numerus::create(2_340_000_000_000)->abbreviate(2))->toBe('2.34T');
        });
    });

    describe('Edge Cases', function (): void {
        test('abbreviates zero', function (): void {
            expect(Numerus::create(0)->abbreviate())->toBe('0');
        });

        test('abbreviates negative thousands', function (): void {
            expect(Numerus::create(-1_000)->abbreviate())->toBe('-1K');
            expect(Numerus::create(-1_500)->abbreviate())->toBe('-2K');
        });

        test('abbreviates negative millions', function (): void {
            expect(Numerus::create(-1_000_000)->abbreviate())->toBe('-1M');
        });

        test('abbreviates small numbers unchanged', function (): void {
            expect(Numerus::create(1)->abbreviate())->toBe('1');
            expect(Numerus::create(500)->abbreviate())->toBe('500');
        });

        test('abbreviates with custom precision', function (): void {
            expect(Numerus::create(1_234)->abbreviate(1))->toBe('1.2K');
            expect(Numerus::create(1_234_567)->abbreviate(3))->toBe('1.235M');
        });

        test('abbreviates boundary values', function (): void {
            expect(Numerus::create(999)->abbreviate())->toBe('999');
            expect(Numerus::create(1_000)->abbreviate())->toBe('1K');
            expect(Numerus::create(999_999)->abbreviate())->toBe('1,000K');
            expect(Numerus::create(1_000_000)->abbreviate())->toBe('1M');
        });
    });
});

describe('Human-Readable Formatting', function (): void {
    describe('Happy Path', function (): void {
        test('formats small numbers', function (): void {
            expect(Numerus::create(100)->forHumans())->toBe('100');
            expect(Numerus::create(999)->forHumans())->toBe('999');
        });

        test('formats thousands', function (): void {
            expect(Numerus::create(1_000)->forHumans())->toBe('1 thousand');
            expect(Numerus::create(1_500)->forHumans())->toBe('2 thousand');
        });

        test('formats millions', function (): void {
            expect(Numerus::create(1_000_000)->forHumans())->toBe('1 million');
            expect(Numerus::create(1_230_000)->forHumans(2))->toBe('1.23 million');
        });

        test('formats billions', function (): void {
            expect(Numerus::create(1_000_000_000)->forHumans())->toBe('1 billion');
        });

        test('formats trillions', function (): void {
            expect(Numerus::create(1_000_000_000_000)->forHumans())->toBe('1 trillion');
        });
    });

    describe('Edge Cases', function (): void {
        test('formats zero', function (): void {
            expect(Numerus::create(0)->forHumans())->toBe('0');
        });

        test('formats negative thousands', function (): void {
            expect(Numerus::create(-1_000)->forHumans())->toBe('-1 thousand');
        });

        test('formats negative millions', function (): void {
            expect(Numerus::create(-1_000_000)->forHumans())->toBe('-1 million');
        });

        test('formats with custom precision', function (): void {
            expect(Numerus::create(1_234_567)->forHumans(3))->toBe('1.235 million');
        });

        test('formats boundary values', function (): void {
            expect(Numerus::create(999)->forHumans())->toBe('999');
            expect(Numerus::create(1_000)->forHumans())->toBe('1 thousand');
        });
    });
});

describe('File Size Formatting', function (): void {
    describe('Happy Path', function (): void {
        test('formats bytes', function (): void {
            expect(Numerus::create(0)->fileSize())->toBe('0 B');
            expect(Numerus::create(512)->fileSize())->toBe('512 B');
            expect(Numerus::create(1_023)->fileSize())->toBe('1 KB');
        });

        test('formats kilobytes', function (): void {
            expect(Numerus::create(1_024)->fileSize())->toBe('1 KB');
            expect(Numerus::create(1_536)->fileSize())->toBe('2 KB');
            expect(Numerus::create(1_024)->fileSize(2))->toBe('1.00 KB');
        });

        test('formats megabytes', function (): void {
            expect(Numerus::create(1_048_576)->fileSize())->toBe('1 MB');
            expect(Numerus::create(1_048_576 * 1.5)->fileSize(1))->toBe('1.5 MB');
        });

        test('formats gigabytes', function (): void {
            expect(Numerus::create(1_073_741_824)->fileSize())->toBe('1 GB');
        });

        test('formats terabytes', function (): void {
            expect(Numerus::create(1_099_511_627_776)->fileSize())->toBe('1 TB');
        });
    });

    describe('Edge Cases', function (): void {
        test('formats single byte', function (): void {
            expect(Numerus::create(1)->fileSize())->toBe('1 B');
        });

        test('formats boundary between bytes and KB', function (): void {
            expect(Numerus::create(1_023)->fileSize())->toBe('1 KB');
            expect(Numerus::create(1_024)->fileSize())->toBe('1 KB');
        });

        test('formats with high precision', function (): void {
            expect(Numerus::create(1_536)->fileSize(3))->toBe('1.500 KB');
        });

        test('formats very small file size', function (): void {
            expect(Numerus::create(100)->fileSize())->toBe('100 B');
        });

        test('formats exactly 1MB', function (): void {
            expect(Numerus::create(1_048_576)->fileSize())->toBe('1 MB');
        });

        test('formats exactly 1GB', function (): void {
            expect(Numerus::create(1_073_741_824)->fileSize())->toBe('1 GB');
        });
    });
});

describe('Number Formatting', function (): void {
    describe('Happy Path', function (): void {
        test('formats integers', function (): void {
            expect(Numerus::create(1_000)->format())->toBe('1,000');
            expect(Numerus::create(100_000)->format())->toBe('100,000');
            expect(Numerus::create(1_000_000)->format())->toBe('1,000,000');
        });

        test('formats decimals', function (): void {
            expect(Numerus::create(1_234.56)->format(2))->toBe('1,234.56');
            expect(Numerus::create(1_234.567)->format(2))->toBe('1,234.57');
        });

        test('respects max precision', function (): void {
            expect(Numerus::create(1_234.5)->format(2, 2))->toBe('1,234.5');
            expect(Numerus::create(1_234.56)->format(0, 2))->toBe('1,234.56');
        });
    });

    describe('Edge Cases', function (): void {
        test('formats zero', function (): void {
            expect(Numerus::create(0)->format())->toBe('0');
        });

        test('formats negative numbers', function (): void {
            expect(Numerus::create(-1_000)->format())->toBe('-1,000');
            expect(Numerus::create(-1_234.56)->format(2))->toBe('-1,234.56');
        });

        test('formats small numbers without thousands separator', function (): void {
            expect(Numerus::create(100)->format())->toBe('100');
            expect(Numerus::create(999)->format())->toBe('999');
        });

        test('formats with zero precision', function (): void {
            expect(Numerus::create(1_234.56)->format(0))->toBe('1,235');
        });

        test('formats with high precision', function (): void {
            expect(Numerus::create(1.123_456_789)->format(5))->toBe('1.12346');
        });

        test('formats single digit', function (): void {
            expect(Numerus::create(5)->format())->toBe('5');
        });

        test('formats decimal without integer part', function (): void {
            expect(Numerus::create(0.5)->format(1))->toBe('0.5');
        });
    });
});

describe('Currency Formatting', function (): void {
    describe('Happy Path', function (): void {
        test('formats USD by default', function (): void {
            $formatted = Numerus::create(1_000)->currency();
            expect($formatted)->toContain('1,000');
        });

        test('formats different currencies', function (): void {
            $eur = Numerus::create(1_000)->currency(in: 'EUR');
            expect($eur)->toContain('1,000');

            $gbp = Numerus::create(1_000)->currency(in: 'GBP');
            expect($gbp)->toContain('1,000');
        });

        test('formats with precision', function (): void {
            $formatted = Numerus::create(1_234.56)->currency(precision: 2);
            expect($formatted)->toContain('1,234.56');
        });
    });

    describe('Edge Cases', function (): void {
        test('formats zero amount', function (): void {
            $formatted = Numerus::create(0)->currency();
            expect($formatted)->toContain('0');
        });

        test('formats negative amount', function (): void {
            $formatted = Numerus::create(-1_000)->currency();
            expect($formatted)->toContain('1,000');
        });

        test('formats small decimal amount', function (): void {
            $formatted = Numerus::create(0.99)->currency(precision: 2);
            expect($formatted)->toContain('0.99');
        });

        test('formats large amount', function (): void {
            $formatted = Numerus::create(1_000_000)->currency();
            expect($formatted)->toContain('1,000,000');
        });
    });
});

describe('Percentage Formatting', function (): void {
    describe('Happy Path', function (): void {
        test('formats simple percentages', function (): void {
            expect(Numerus::create(10)->percentage())->toBe('10%');
            expect(Numerus::create(50)->percentage())->toBe('50%');
            expect(Numerus::create(100)->percentage())->toBe('100%');
        });

        test('formats with precision', function (): void {
            expect(Numerus::create(10.123)->percentage(2))->toBe('10.12%');
            expect(Numerus::create(99.999)->percentage(1))->toBe('100.0%');
        });

        test('respects max precision', function (): void {
            expect(Numerus::create(10.1)->percentage(0, 2))->toBe('10.1%');
            expect(Numerus::create(10.12)->percentage(0, 2))->toBe('10.12%');
        });
    });

    describe('Edge Cases', function (): void {
        test('formats zero percentage', function (): void {
            expect(Numerus::create(0)->percentage())->toBe('0%');
        });

        test('formats negative percentage', function (): void {
            expect(Numerus::create(-10)->percentage())->toBe('-10%');
            expect(Numerus::create(-10.5)->percentage(1))->toBe('-10.5%');
        });

        test('formats very small percentage', function (): void {
            expect(Numerus::create(0.01)->percentage(2))->toBe('0.01%');
        });

        test('formats very large percentage', function (): void {
            expect(Numerus::create(1_000)->percentage())->toBe('1,000%');
        });

        test('formats with zero precision', function (): void {
            expect(Numerus::create(10.9)->percentage(0))->toBe('11%');
        });

        test('formats with high precision', function (): void {
            expect(Numerus::create(33.333_333)->percentage(4))->toBe('33.3333%');
        });
    });
});

describe('Ordinal Formatting', function (): void {
    describe('Happy Path', function (): void {
        test('formats basic ordinals', function (): void {
            expect(Numerus::create(1)->ordinal())->toBe('1st');
            expect(Numerus::create(2)->ordinal())->toBe('2nd');
            expect(Numerus::create(3)->ordinal())->toBe('3rd');
            expect(Numerus::create(4)->ordinal())->toBe('4th');
        });

        test('formats teen ordinals', function (): void {
            expect(Numerus::create(11)->ordinal())->toBe('11th');
            expect(Numerus::create(12)->ordinal())->toBe('12th');
            expect(Numerus::create(13)->ordinal())->toBe('13th');
        });

        test('formats twenty-plus ordinals', function (): void {
            expect(Numerus::create(21)->ordinal())->toBe('21st');
            expect(Numerus::create(22)->ordinal())->toBe('22nd');
            expect(Numerus::create(23)->ordinal())->toBe('23rd');
            expect(Numerus::create(24)->ordinal())->toBe('24th');
        });

        test('formats large ordinals', function (): void {
            expect(Numerus::create(101)->ordinal())->toBe('101st');
            expect(Numerus::create(1_000)->ordinal())->toBe('1,000th');
        });
    });

    describe('Edge Cases', function (): void {
        test('formats zero ordinal', function (): void {
            expect(Numerus::create(0)->ordinal())->toBe('0th');
        });

        test('formats negative ordinals', function (): void {
            expect(Numerus::create(-1)->ordinal())->toBe('−1st');
            expect(Numerus::create(-2)->ordinal())->toBe('−2nd');
            expect(Numerus::create(-3)->ordinal())->toBe('−3rd');
        });

        test('formats 10th', function (): void {
            expect(Numerus::create(10)->ordinal())->toBe('10th');
        });

        test('formats hundreds ordinals', function (): void {
            expect(Numerus::create(100)->ordinal())->toBe('100th');
            expect(Numerus::create(200)->ordinal())->toBe('200th');
        });

        test('formats 111th, 112th, 113th', function (): void {
            expect(Numerus::create(111)->ordinal())->toBe('111th');
            expect(Numerus::create(112)->ordinal())->toBe('112th');
            expect(Numerus::create(113)->ordinal())->toBe('113th');
        });

        test('formats 1000th and beyond', function (): void {
            expect(Numerus::create(1_001)->ordinal())->toBe('1,001st');
            expect(Numerus::create(1_002)->ordinal())->toBe('1,002nd');
        });
    });
});

describe('Spelling', function (): void {
    describe('Happy Path', function (): void {
        test('spells basic numbers', function (): void {
            expect(Numerus::create(1)->spell())->toBe('one');
            expect(Numerus::create(10)->spell())->toBe('ten');
            expect(Numerus::create(20)->spell())->toBe('twenty');
        });

        test('spells compound numbers', function (): void {
            expect(Numerus::create(21)->spell())->toBe('twenty-one');
            expect(Numerus::create(99)->spell())->toBe('ninety-nine');
        });

        test('spells hundreds', function (): void {
            expect(Numerus::create(100)->spell())->toBe('one hundred');
            expect(Numerus::create(102)->spell())->toBe('one hundred two');
        });
    });

    describe('Edge Cases', function (): void {
        test('spells zero', function (): void {
            expect(Numerus::create(0)->spell())->toBe('zero');
        });

        test('spells teens', function (): void {
            expect(Numerus::create(11)->spell())->toBe('eleven');
            expect(Numerus::create(12)->spell())->toBe('twelve');
            expect(Numerus::create(13)->spell())->toBe('thirteen');
            expect(Numerus::create(15)->spell())->toBe('fifteen');
        });

        test('spells negative numbers', function (): void {
            expect(Numerus::create(-5)->spell())->toBe('minus five');
            expect(Numerus::create(-21)->spell())->toBe('minus twenty-one');
        });

        test('spells exact tens', function (): void {
            expect(Numerus::create(30)->spell())->toBe('thirty');
            expect(Numerus::create(40)->spell())->toBe('forty');
            expect(Numerus::create(50)->spell())->toBe('fifty');
        });
    });
});

describe('Ordinal Spelling', function (): void {
    describe('Happy Path', function (): void {
        test('spells basic ordinals', function (): void {
            expect(Numerus::create(1)->spellOrdinal())->toBe('first');
            expect(Numerus::create(2)->spellOrdinal())->toBe('second');
            expect(Numerus::create(3)->spellOrdinal())->toBe('third');
            expect(Numerus::create(4)->spellOrdinal())->toBe('fourth');
        });

        test('spells teen ordinals', function (): void {
            expect(Numerus::create(11)->spellOrdinal())->toBe('eleventh');
            expect(Numerus::create(12)->spellOrdinal())->toBe('twelfth');
            expect(Numerus::create(13)->spellOrdinal())->toBe('thirteenth');
        });

        test('spells compound ordinals', function (): void {
            expect(Numerus::create(21)->spellOrdinal())->toBe('twenty-first');
            expect(Numerus::create(32)->spellOrdinal())->toBe('thirty-second');
        });
    });

    describe('Edge Cases', function (): void {
        test('spells zeroth', function (): void {
            expect(Numerus::create(0)->spellOrdinal())->toBe('zeroth');
        });

        test('spells fifth and eighth', function (): void {
            expect(Numerus::create(5)->spellOrdinal())->toBe('fifth');
            expect(Numerus::create(8)->spellOrdinal())->toBe('eighth');
        });

        test('spells tenth', function (): void {
            expect(Numerus::create(10)->spellOrdinal())->toBe('tenth');
        });

        test('spells exact tens ordinals', function (): void {
            expect(Numerus::create(20)->spellOrdinal())->toBe('twentieth');
            expect(Numerus::create(30)->spellOrdinal())->toBe('thirtieth');
        });
    });
});

describe('Trim Operation', function (): void {
    describe('Happy Path', function (): void {
        test('removes trailing zeros', function (): void {
            expect(Numerus::create(12.0)->trim()->value())->toBe(12);
            expect(Numerus::create(12.30)->trim()->value())->toBe(12.3);
            expect(Numerus::create(12.300)->trim()->value())->toBe(12.3);
        });

        test('preserves significant decimals', function (): void {
            expect(Numerus::create(12.34)->trim()->value())->toBe(12.34);
            expect(Numerus::create(12.01)->trim()->value())->toBe(12.01);
        });

        test('returns new instance', function (): void {
            $original = Numerus::create(12.30);
            $trimmed = $original->trim();

            expect($original->value())->toBe(12.30);
            expect($trimmed->value())->toBe(12.3);
        });

        test('returns same instance for integers', function (): void {
            $num = Numerus::create(42);
            $trimmed = $num->trim();

            expect($trimmed)->toBe($num);
            expect($trimmed->value())->toBe(42);
        });
    });

    describe('Edge Cases', function (): void {
        test('trims multiple trailing zeros', function (): void {
            expect(Numerus::create(12.000_00)->trim()->value())->toBe(12);
        });

        test('trims zero value', function (): void {
            expect(Numerus::create(0.0)->trim()->value())->toBe(0);
        });

        test('trims negative numbers', function (): void {
            expect(Numerus::create(-12.30)->trim()->value())->toBe(-12.3);
        });

        test('preserves non-trailing zeros', function (): void {
            expect(Numerus::create(10.10)->trim()->value())->toBe(10.1);
            expect(Numerus::create(100.00)->trim()->value())->toBe(100);
        });
    });
});

describe('Static Configuration', function (): void {
    afterEach(function (): void {
        Numerus::useLocale('en');
        Numerus::useCurrency('USD');
    });

    describe('Happy Path', function (): void {
        test('gets default locale', function (): void {
            expect(Numerus::defaultLocale())->toBe('en');
        });

        test('sets default locale', function (): void {
            Numerus::useLocale('fr');
            expect(Numerus::defaultLocale())->toBe('fr');
        });

        test('gets default currency', function (): void {
            expect(Numerus::defaultCurrency())->toBe('USD');
        });

        test('sets default currency', function (): void {
            Numerus::useCurrency('EUR');
            expect(Numerus::defaultCurrency())->toBe('EUR');
        });

        test('executes with temporary locale', function (): void {
            $result = Numerus::withLocale('de', fn (): string => Numerus::create(1_000)->format());

            expect($result)->toContain('1');
            expect(Numerus::defaultLocale())->toBe('en');
        });

        test('executes with temporary currency', function (): void {
            $result = Numerus::withCurrency('EUR', Numerus::defaultCurrency(...));

            expect($result)->toBe('EUR');
            expect(Numerus::defaultCurrency())->toBe('USD');
        });
    });

    describe('Edge Cases', function (): void {
        test('locale changes persist', function (): void {
            Numerus::useLocale('de');
            expect(Numerus::defaultLocale())->toBe('de');
            Numerus::useLocale('en');
            expect(Numerus::defaultLocale())->toBe('en');
        });

        test('currency changes persist', function (): void {
            Numerus::useCurrency('GBP');
            expect(Numerus::defaultCurrency())->toBe('GBP');
            Numerus::useCurrency('USD');
            expect(Numerus::defaultCurrency())->toBe('USD');
        });

        test('temporary locale restores after exception', function (): void {
            try {
                Numerus::withLocale('de', function (): void {
                    throw new Exception('Test exception');
                });
            } catch (Exception) {
                // Expected
            }

            expect(Numerus::defaultLocale())->toBe('en');
        });
    });
});

describe('Static Parsing', function (): void {
    describe('Happy Path', function (): void {
        test('parses integers in US locale', function (): void {
            $num = Numerus::parseInt('42', 'en_US');
            expect($num->value())->toBe(42);

            $num = Numerus::parseInt('1,234', 'en_US');
            expect($num->value())->toBe(1_234);

            $num = Numerus::parseInt('1,234,567', 'en_US');
            expect($num->value())->toBe(1_234_567);
        });

        test('parses integers in German locale', function (): void {
            $num = Numerus::parseInt('1.234', 'de_DE');
            expect($num->value())->toBe(1_234);

            $num = Numerus::parseInt('1.234.567', 'de_DE');
            expect($num->value())->toBe(1_234_567);
        });

        test('parses integers in French locale', function (): void {
            $num = Numerus::parseInt('1 234', 'fr_FR');
            expect($num->value())->toBe(1_234);

            $num = Numerus::parseInt('1 234 567', 'fr_FR');
            expect($num->value())->toBe(1_234_567);
        });

        test('parses integers in Spanish locale', function (): void {
            $num = Numerus::parseInt('1.234', 'es_ES');
            expect($num->value())->toBe(1_234);

            $num = Numerus::parseInt('1.234.567', 'es_ES');
            expect($num->value())->toBe(1_234_567);
        });

        test('parses integers in Indian locale', function (): void {
            $num = Numerus::parseInt('1,234', 'en_IN');
            expect($num->value())->toBe(1_234);

            $num = Numerus::parseInt('12,34,567', 'en_IN');
            expect($num->value())->toBe(1_234_567);
        });

        test('parses floats in US locale', function (): void {
            $num = Numerus::parseFloat('42.5', 'en_US');
            expect($num->value())->toBe(42.5);

            $num = Numerus::parseFloat('1,234.56', 'en_US');
            expect($num->value())->toBe(1_234.56);

            $num = Numerus::parseFloat('1,234,567.89', 'en_US');
            expect($num->value())->toBe(1_234_567.89);
        });

        test('parses floats in German locale', function (): void {
            $num = Numerus::parseFloat('1.234,56', 'de_DE');
            expect($num->value())->toBe(1_234.56);

            $num = Numerus::parseFloat('1.234.567,89', 'de_DE');
            expect($num->value())->toBe(1_234_567.89);
        });

        test('parses floats in French locale', function (): void {
            $num = Numerus::parseFloat('1 234,56', 'fr_FR');
            expect($num->value())->toBe(1_234.56);

            $num = Numerus::parseFloat('1 234 567,89', 'fr_FR');
            expect($num->value())->toBe(1_234_567.89);
        });

        test('parses floats in Spanish locale', function (): void {
            $num = Numerus::parseFloat('1.234,56', 'es_ES');
            expect($num->value())->toBe(1_234.56);

            $num = Numerus::parseFloat('1.234.567,89', 'es_ES');
            expect($num->value())->toBe(1_234_567.89);
        });

        test('parses floats in Indian locale', function (): void {
            $num = Numerus::parseFloat('1,234.56', 'en_IN');
            expect($num->value())->toBe(1_234.56);

            $num = Numerus::parseFloat('12,34,567.89', 'en_IN');
            expect($num->value())->toBe(1_234_567.89);
        });

        test('parses floats in Swiss locale', function (): void {
            $num = Numerus::parseFloat("1'234.56", 'de_CH');
            expect($num->value())->toBe(1_234.56);

            $num = Numerus::parseFloat("1'234'567.89", 'de_CH');
            expect($num->value())->toBe(1_234_567.89);
        });

        test('parses negative numbers', function (): void {
            $num = Numerus::parseInt('-1,234', 'en_US');
            expect($num->value())->toBe(-1_234);

            $num = Numerus::parseFloat('-1,234.56', 'en_US');
            expect($num->value())->toBe(-1_234.56);

            $num = Numerus::parseFloat('-1.234,56', 'de_DE');
            expect($num->value())->toBe(-1_234.56);
        });

        test('generates pairs', function (): void {
            $pairs = Numerus::pairs(25, 10);

            expect($pairs)->toBe([
                [1, 10],
                [11, 20],
                [21, 25],
            ]);
        });

        test('generates pairs with custom offset', function (): void {
            $pairs = Numerus::pairs(25, 10, 0);

            expect($pairs)->toBe([
                [0, 9],
                [10, 19],
                [20, 25],
            ]);
        });
    });

    describe('Sad Path', function (): void {
        test('throws exception for invalid integer', function (): void {
            Numerus::parseInt('not a number');
        })->throws(InvalidArgumentException::class, "Unable to parse 'not a number' as integer");

        test('throws exception for invalid float', function (): void {
            Numerus::parseFloat('not a number');
        })->throws(InvalidArgumentException::class, "Unable to parse 'not a number' as float");
    });

    describe('Edge Cases', function (): void {
        test('parses zero', function (): void {
            $num = Numerus::parseInt('0', 'en_US');
            expect($num->value())->toBe(0);

            $num = Numerus::parseFloat('0.0', 'en_US');
            expect($num->value())->toBe(0.0);
        });

        test('parses simple integers without separators', function (): void {
            $num = Numerus::parseInt('42', 'en_US');
            expect($num->value())->toBe(42);
        });

        test('parses simple floats without separators', function (): void {
            $num = Numerus::parseFloat('42.5', 'en_US');
            expect($num->value())->toBe(42.5);
        });

        test('generates pairs with exact division', function (): void {
            $pairs = Numerus::pairs(20, 10);

            expect($pairs)->toBe([
                [1, 10],
                [11, 20],
            ]);
        });

        test('generates pairs with size of 1', function (): void {
            $pairs = Numerus::pairs(5, 1);

            expect($pairs)->toBe([
                [1, 1],
                [2, 2],
                [3, 3],
                [4, 4],
            ]);
        });

        test('generates single pair when total less than size', function (): void {
            $pairs = Numerus::pairs(5, 10);

            expect($pairs)->toBe([
                [1, 5],
            ]);
        });
    });
});
