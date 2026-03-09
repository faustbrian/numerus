## Table of Contents

1. [Basic Usage](#doc-docs-basic-usage)
2. [Comparisons](#doc-docs-comparisons)
3. [Chaining](#doc-docs-chaining)
4. [Formatting](#doc-docs-formatting)
<a id="doc-docs-basic-usage"></a>

Numerus is an immutable numeric value object that provides chainable arithmetic operations.

## Creating Numerus Instances

There are two ways to create Numerus instances:

### Using the Static Factory

```php
use Cline\Numerus\Numerus;

$num = Numerus::create(42);
$decimal = Numerus::create(42.5);
$negative = Numerus::create(-10);
```

### Using the Helper Function (Recommended)

The `numerus()` helper function provides a more concise syntax:

```php
use function Cline\Numerus\numerus;

$num = numerus(42);
$decimal = numerus(42.5);
$negative = numerus(-10);
```

Both methods create identical instances. The helper function is recommended for cleaner, more readable code.

## Arithmetic Operations

All arithmetic operations return a new Numerus instance, preserving immutability.

### Addition

```php
use function Cline\Numerus\numerus;

$num = numerus(10);
$result = $num->plus(5);
// $result->value() === 15
// $num->value() === 10 (original unchanged)

// Can also add another Numerus instance
$num1 = numerus(10);
$num2 = numerus(5);
$result = $num1->plus($num2);
// $result->value() === 15
```

### Subtraction

```php
$num = numerus(10);
$result = $num->minus(3);
// $result->value() === 7
```

### Multiplication

```php
$num = numerus(10);
$result = $num->multiplyBy(3);
// $result->value() === 30
```

### Division

```php
$num = numerus(10);
$result = $num->divideBy(2);
// $result->value() === 5.0

// Division by zero throws InvalidArgumentException
try {
    $num->divideBy(0);
} catch (InvalidArgumentException $e) {
    // Handle division by zero
}
```

### Modulo

```php
$num = numerus(10);
$result = $num->mod(3);
// $result->value() === 1
```

### Power

```php
$num = numerus(2);
$result = $num->power(3);
// $result->value() === 8
```

### Square Root

```php
$num = numerus(16);
$result = $num->sqrt();
// $result->value() === 4.0

// Square root of negative throws InvalidArgumentException
```

## Static Array Aggregation

Use static aggregation helpers when values come from arrays of items.

```php
use Cline\Numerus\Numerus;

$items = [
    ['quantity' => 2, 'unitValue' => 10],
    ['quantity' => 3, 'unitValue' => 20],
    ['quantity' => 1, 'unitValue' => 30],
];

// Sum a single field (key or callback)
$unitValueTotal = Numerus::sumBy($items, 'unitValue')->value(); // 60
$unitValueTotal = Numerus::sumBy($items, fn (array $item) => $item['unitValue'])->value(); // 60

// Sum products (common "quantity * unitValue" pattern)
$itemsTotal = Numerus::sumProductBy($items, 'quantity', 'unitValue')->value(); // 110

// Aggregate helpers for common projections
$averageUnitValue = Numerus::averageBy($items, 'unitValue')->value(); // 20
$weightedAverageUnitValue = Numerus::weightedAverageBy($items, 'quantity', 'unitValue')->value(); // 18.3333333333
$minUnitValue = Numerus::minBy($items, 'unitValue')->value(); // 10
$maxUnitValue = Numerus::maxBy($items, 'unitValue')->value(); // 30
$variance = Numerus::varianceBy($items, 'unitValue')->value();
$standardDeviation = Numerus::standardDeviationBy($items, 'unitValue')->value();
$sampleVariance = Numerus::sampleVarianceBy($items, 'unitValue')->value();
$sampleStandardDeviation = Numerus::sampleStandardDeviationBy($items, 'unitValue')->value();
$median = Numerus::medianBy($items, 'unitValue')->value();
$p90 = Numerus::percentileBy($items, 90, 'unitValue')->value();
$p90NearestRank = Numerus::percentileBy($items, 90, 'unitValue', method: 'nearest_rank')->value();
$mode = Numerus::modeBy($items, 'unitValue')->value();
$modes = Numerus::modesBy($items, 'unitValue');
$quantityToValueRatio = Numerus::ratioBy($items, 'quantity', 'unitValue')->value();
$countAbove20 = Numerus::countWhere($items, fn (array $item) => $item['unitValue'] > 20);
$countsByUnitValue = Numerus::countBy($items, 'unitValue');
$sumSquares = Numerus::sumOfSquaresBy($items, 'unitValue')->value();

// Callback selectors can also receive item index/key
$sumWithIndex = Numerus::sumBy(
    $items,
    fn (array $item, int $key) => $item['unitValue'] + $key
)->value(); // 63

// Dot-path selectors are supported for nested payloads
$nestedItems = [
    ['pricing' => ['unit' => ['value' => 10]]],
    ['pricing' => ['unit' => ['value' => 20]]],
];
$nestedTotal = Numerus::sumBy($nestedItems, 'pricing.unit.value')->value(); // 30
```

## Rounding Operations

### Absolute Value

```php
$num = numerus(-42);
$result = $num->abs();
// $result->value() === 42
```

### Ceiling

```php
$num = numerus(42.3);
$result = $num->ceil();
// $result->value() === 43
```

### Floor

```php
$num = numerus(42.9);
$result = $num->floor();
// $result->value() === 42
```

### Round

```php
use RoundingMode;

$num = numerus(42.6);
$result = $num->round();
// $result->value() === 43.0

// With precision
$num = numerus(42.456);
$result = $num->round(2);
// $result->value() === 42.46

// With RoundingMode enum
$num = numerus(2.5);
$result = $num->round(0, RoundingMode::HalfEven); // Banker's rounding
// $result->value() === 2.0

// Convenience methods for specific rounding modes
$num = numerus(2.5);
$num->roundAwayFromZero();       // 3
$num->roundTowardsZero();        // 2
$num->roundPositiveInfinity();   // 3
$num->roundNegativeInfinity();   // 2
$num->roundHalfAwayFromZero();   // 3 (default behavior)
$num->roundHalfTowardsZero();    // 2
$num->roundHalfEven();           // 2 (banker's rounding)
$num->roundHalfOdd();            // 3
```

### Negate

```php
$num = numerus(42);
$result = $num->negate();
// $result->value() === -42
```

## Retrieving the Value

```php
$num = numerus(42);

// Get raw value
$value = $num->value(); // 42

// Convert to specific types
$int = $num->toInt();     // 42
$float = $num->toFloat(); // 42.0
$string = $num->toString(); // "42"
$string = (string) $num;    // "42"
```

## JSON Serialization

Numerus instances implement `JsonSerializable` and automatically convert to their numeric values when JSON encoded:

```php
$num = numerus(42);
echo json_encode($num); // 42

$decimal = numerus(42.5);
echo json_encode($decimal); // 42.5

// Works seamlessly in arrays and objects
$data = [
    'price' => numerus(99.99),
    'quantity' => numerus(5),
    'total' => numerus(99.99)->multiplyBy(5)
];
echo json_encode($data);
// {"price":99.99,"quantity":5,"total":499.95}

// In API responses
return response()->json([
    'amount' => numerus(100)->addPercent(20),
    'tax' => numerus(20)
]);
// {"amount":120,"tax":20}
```

## Extending with Macros

Both `Numerus` and `Percentage` support Laravel's Macroable trait, allowing you to add custom methods:

### Numerus Macros

```php
use Cline\Numerus\Numerus;
use function Cline\Numerus\numerus;

// Add instance macros
Numerus::macro('squared', fn () => $this->multiplyBy($this));
Numerus::macro('cubed', fn () => $this->power(3));
Numerus::macro('doubled', fn () => $this->multiplyBy(2));

$result = numerus(5)->squared(); // 25
$result = numerus(3)->cubed();   // 27
$result = numerus(10)->doubled(); // 20

// Add static macros
Numerus::macro('fromHundred', fn (int|float $value) =>
    Numerus::create(100)->minus($value)
);

$result = Numerus::fromHundred(30); // 70

// Macros with parameters
Numerus::macro('multiplyAndAdd', fn (int|float $multiplier, int|float $addend) =>
    $this->multiplyBy($multiplier)->plus($addend)
);

$result = numerus(10)->multiplyAndAdd(3, 5); // 35

// Chain macros with native methods
Numerus::macro('percentage', fn (int|float $percent) =>
    $this->multiplyBy($percent)->divideBy(100)
);

$result = numerus(200)->percentage(15)->round(2); // 30.00
```

### Percentage Macros

```php
use Cline\Numerus\Percentage;

// Add convenience methods
Percentage::macro('isHigh', fn (float $value, float $threshold = 50) =>
    $value >= $threshold
);

Percentage::isHigh(75); // true
Percentage::isHigh(30); // false
Percentage::isHigh(45, 40); // true

// Add domain-specific calculations
Percentage::macro('taxAmount', fn (float $taxRate, int|float $price) =>
    Percentage::calculate($taxRate, $price)
);

Percentage::macro('discountPrice', fn (float $discount, float $price) =>
    Percentage::subtract($discount, $price)
);

$tax = Percentage::taxAmount(10, 100);      // 10.0
$final = Percentage::discountPrice(20, 100); // 80.0

// Check if macro exists
if (Numerus::hasMacro('squared')) {
    // Macro is available
}

// Remove all macros (useful for testing)
Numerus::flushMacros();
Percentage::flushMacros();
```

### Practical Macro Examples

```php
// Financial calculations
Numerus::macro('withTax', fn (float $taxRate) =>
    $this->addPercent($taxRate)
);

Numerus::macro('afterDiscount', fn (float $discountRate) =>
    $this->subtractPercent($discountRate)
);

$price = numerus(100)
    ->afterDiscount(10)  // 90
    ->withTax(20);       // 108

// Unit conversions
Numerus::macro('toKilobytes', fn () => $this->divideBy(1024));
Numerus::macro('toMegabytes', fn () => $this->divideBy(1024 * 1024));

$bytes = numerus(1_048_576);
$mb = $bytes->toMegabytes(); // 1.0

// Mathematical helpers
Numerus::macro('isEvenNumber', fn () => $this->isInteger() && $this->isEven());
Numerus::macro('isPrime', function () {
    if ($this->lessThanOrEqual(1)) return false;
    if ($this->equals(2)) return true;
    if ($this->isEven()) return false;

    $sqrt = $this->sqrt()->toInt();
    for ($i = 3; $i <= $sqrt; $i += 2) {
        if ($this->mod($i)->isZero()) return false;
    }
    return true;
});

numerus(17)->isPrime(); // true
numerus(20)->isPrime(); // false
```

<a id="doc-docs-comparisons"></a>

Numerus provides rich comparison operations for numeric values.

## Equality

```php
use function Cline\Numerus\numerus;

$num = numerus(42);

$num->equals(42);        // true
$num->equals(43);        // false
$num->equals(numerus(42)); // true

$num->notEquals(43);     // true
$num->notEquals(42);     // false
```

## Negated Assertions

Use `not()` for inverted comparison logic:

```php
$num = numerus(10);

// Negated equality
$num->not()->equals(5);              // true (10 != 5)
$num->not()->equals(10);             // false (10 == 10)

// Negated comparisons
$num->not()->greaterThan(20);        // true (10 <= 20)
$num->not()->lessThan(5);            // true (10 >= 5)

// Negated range checks
$num->not()->between(5, 15);         // false (10 is within range)
$num->not()->between(20, 30);        // true (10 is outside range)
```

## Relational Comparisons

### Greater Than

```php
$num = numerus(42);

$num->greaterThan(41);  // true
$num->greaterThan(42);  // false
$num->greaterThan(43);  // false
```

### Greater Than or Equal

```php
$num = numerus(42);

$num->greaterThanOrEqual(41);  // true
$num->greaterThanOrEqual(42);  // true
$num->greaterThanOrEqual(43);  // false
```

### Less Than

```php
$num = numerus(42);

$num->lessThan(43);  // true
$num->lessThan(42);  // false
$num->lessThan(41);  // false
```

### Less Than or Equal

```php
$num = numerus(42);

$num->lessThanOrEqual(43);  // true
$num->lessThanOrEqual(42);  // true
$num->lessThanOrEqual(41);  // false
```

## Min/Max Operations

### Minimum

```php
$num = numerus(42);
$result = $num->min(30);
// $result->value() === 30

$result = $num->min(50);
// $result->value() === 42
```

### Maximum

```php
$num = numerus(42);
$result = $num->max(50);
// $result->value() === 50

$result = $num->max(30);
// $result->value() === 42
```

### Clamp

Clamp constrains a value between a minimum and maximum:

```php
$num = numerus(100);
$result = $num->clamp(0, 50);
// $result->value() === 50 (clamped to max)

$num = numerus(-10);
$result = $num->clamp(0, 50);
// $result->value() === 0 (clamped to min)

$num = numerus(25);
$result = $num->clamp(0, 50);
// $result->value() === 25 (within range)

// Using Numerus instances
$result = $num->clamp(numerus(0), numerus(50));
```

## Type Checking

### Positive/Negative/Zero

```php
numerus(42)->isPositive();   // true
numerus(-42)->isPositive();  // false
numerus(0)->isPositive();    // false

numerus(-42)->isNegative();  // true
numerus(42)->isNegative();   // false
numerus(0)->isNegative();    // false

numerus(0)->isZero();      // true
numerus(0.0)->isZero();    // true
numerus(42)->isZero();     // false
```

### Even/Odd

Note: These checks only work with integer values.

```php
numerus(42)->isEven();  // true
numerus(43)->isEven();  // false
numerus(0)->isEven();   // true

numerus(43)->isOdd();   // true
numerus(42)->isOdd();   // false
numerus(0)->isOdd();    // false
```

## Practical Examples

### Validate Range

```php
function validateScore(int $score): bool
{
    return numerus($score)
        ->greaterThanOrEqual(0)
        && numerus($score)->lessThanOrEqual(100);
}

// Using negated assertions
function isInvalidScore(int $score): bool
{
    return numerus($score)->not()->between(0, 100);
}
```

### Ensure Non-Negative

```php
function ensurePositive(int|float $value): Numerus
{
    $num = numerus($value);

    return $num->isNegative() ? $num->abs() : $num;
}
```

### Constrain Value

```php
function normalizePercentage(float $value): float
{
    return numerus($value)
        ->clamp(0, 100)
        ->value();
}
```

<a id="doc-docs-chaining"></a>

One of Numerus's most powerful features is the ability to chain operations together, creating fluent and readable numeric computations.

## Basic Chaining

Every operation returns a new Numerus instance, allowing method chaining:

```php
use function Cline\Numerus\numerus;

$result = numerus(10)
    ->plus(5)
    ->minus(3)
    ->multiplyBy(2)
    ->divideBy(4);

// $result->value() === 6.0
```

## Complex Calculations

### Calculate Percentage Increase

```php
function calculatePercentageIncrease(float $original, float $new): float
{
    return numerus($new)
        ->minus($original)
        ->divideBy($original)
        ->multiplyBy(100)
        ->round(2)
        ->value();
}

$increase = calculatePercentageIncrease(50, 75);
// $increase === 50.0 (50% increase)
```

### Calculate Average with Constraints

```php
function constrainedAverage(array $values, int $min, int $max): float
{
    $sum = array_reduce(
        $values,
        fn($carry, $value) => $carry->plus($value),
        numerus(0)
    );

    return $sum
        ->divideBy(count($values))
        ->clamp($min, $max)
        ->round(2)
        ->value();
}
```

### Distance Formula

```php
function distance(float $x1, float $y1, float $x2, float $y2): float
{
    $dx = numerus($x2)->minus($x1)->power(2);
    $dy = numerus($y2)->minus($y1)->power(2);

    return $dx->plus($dy->value())->sqrt()->value();
}

$dist = distance(0, 0, 3, 4);
// $dist === 5.0
```

## Conditional Chaining

### Apply Operation Based on Condition

```php
function applyDiscount(float $price, bool $isPremium): float
{
    $num = numerus($price);

    if ($isPremium) {
        return $num
            ->multiplyBy(0.8)  // 20% discount
            ->round(2)
            ->value();
    }

    return $num
        ->multiplyBy(0.95)  // 5% discount
        ->round(2)
        ->value();
}
```

### Normalize and Transform

```php
function normalizeAndScale(float $value, float $min, float $max, float $scale = 1.0): float
{
    return numerus($value)
        ->clamp($min, $max)
        ->minus($min)
        ->divideBy($max - $min)
        ->multiplyBy($scale)
        ->value();
}

$normalized = normalizeAndScale(75, 0, 100, 10);
// $normalized === 7.5
```

## Working with Collections

### Transform Array of Values

```php
$prices = [10.99, 25.50, 99.99];

$discounted = array_map(
    fn($price) => numerus($price)
        ->multiplyBy(0.9)
        ->round(2)
        ->value(),
    $prices
);

// $discounted === [9.89, 22.95, 89.99]
```

### Calculate Statistics

```php
function calculateStats(array $values): array
{
    $count = count($values);
    $sum = array_reduce(
        $values,
        fn($carry, $val) => $carry->plus($val),
        numerus(0)
    );

    $mean = $sum->divideBy($count);

    $variance = array_reduce(
        $values,
        fn($carry, $val) => $carry->plus(
            numerus($val)->minus($mean->value())->power(2)->value()
        ),
        numerus(0)
    )->divideBy($count);

    return [
        'sum' => $sum->value(),
        'mean' => $mean->round(2)->value(),
        'variance' => $variance->round(2)->value(),
        'stddev' => $variance->sqrt()->round(2)->value(),
    ];
}

$stats = calculateStats([2, 4, 4, 4, 5, 5, 7, 9]);
```

## Practical Examples

### Currency Conversion with Rounding

```php
use RoundingMode;

function convertCurrency(float $amount, float $rate): float
{
    return numerus($amount)
        ->multiplyBy($rate)
        ->round(2)
        ->value();
}

$euros = convertCurrency(100, 0.85);
// $euros === 85.0

// Using banker's rounding for financial accuracy
function convertCurrencyBankers(float $amount, float $rate): float
{
    return numerus($amount)
        ->multiplyBy($rate)
        ->round(2, RoundingMode::HalfEven)
        ->value();
}

// Or use the convenience method
function convertCurrencyBankers2(float $amount, float $rate): float
{
    return numerus($amount)
        ->multiplyBy($rate)
        ->roundHalfEven(2)
        ->value();
}
```

### Calculate Tax

```php
function calculateTotalWithTax(float $subtotal, float $taxRate): float
{
    return numerus($subtotal)
        ->multiplyBy(numerus(1)->plus($taxRate)->value())
        ->round(2)
        ->value();
}

$total = calculateTotalWithTax(100, 0.20);
// $total === 120.0
```

### Compound Interest

```php
function compoundInterest(
    float $principal,
    float $rate,
    int $years,
    int $compoundsPerYear = 1
): float {
    return numerus(1)
        ->plus($rate / $compoundsPerYear)
        ->power($compoundsPerYear * $years)
        ->multiplyBy($principal)
        ->round(2)
        ->value();
}

$amount = compoundInterest(1000, 0.05, 10, 12);
// Monthly compound interest over 10 years
```

## Immutability Benefits

The immutability of Numerus ensures that chained operations don't affect earlier values:

```php
$base = numerus(100);
$withTax = $base->multiplyBy(1.20);
$discounted = $base->multiplyBy(0.90);

// $base->value() === 100 (unchanged)
// $withTax->value() === 120.0
// $discounted->value() === 90.0
```

This allows you to derive multiple calculations from the same base value safely.

<a id="doc-docs-formatting"></a>

Numerus provides rich formatting capabilities inspired by Laravel's Number class, supporting internationalization and various number representations.

## Abbreviation

Format large numbers with unit abbreviations (K, M, B, T):

```php
use function Cline\Numerus\numerus;

numerus(1000)->abbreviate();           // "1K"
numerus(1500)->abbreviate();           // "2K"
numerus(1000000)->abbreviate();        // "1M"
numerus(1230000)->abbreviate(2);       // "1.23M"
numerus(1000000000)->abbreviate();     // "1B"
numerus(1000000000000)->abbreviate();  // "1T"
```

## Human-Readable Format

Convert numbers to word-based representations:

```php
numerus(1000)->forHumans();        // "1 thousand"
numerus(1500)->forHumans();        // "2 thousand"
numerus(1000000)->forHumans();     // "1 million"
numerus(1230000)->forHumans(2);    // "1.23 million"
numerus(1000000000)->forHumans();  // "1 billion"
```

With locale support:

```php
numerus(1000000)->forHumans(locale: 'fr');  // "1 million" (French)
numerus(1000000)->forHumans(locale: 'de');  // "1 Million" (German)
```

## File Size Formatting

Format byte values as readable file sizes:

```php
numerus(0)->fileSize();           // "0 B"
numerus(512)->fileSize();         // "512 B"
numerus(1024)->fileSize();        // "1 KB"
numerus(1024 * 1024)->fileSize(); // "1 MB"
numerus(1073741824)->fileSize();  // "1 GB"

// With precision
numerus(1536)->fileSize(2);       // "1.50 KB"
numerus(1048576 * 1.5)->fileSize(1);  // "1.5 MB"
```

## Number Formatting

Format numbers with thousands separators and decimal precision:

```php
numerus(1000)->format();          // "1,000"
numerus(100000)->format();        // "100,000"
numerus(1234.56)->format(2);      // "1,234.56"

// With max precision
numerus(1234.5)->format(2, 2);    // "1,234.50"
```

Locale-specific formatting:

```php
numerus(1000)->format(locale: 'de');     // "1.000" (German)
numerus(1234.56)->format(2, locale: 'fr');  // "1 234,56" (French)
```

## Currency Formatting

Format values as currency:

```php
numerus(1000)->currency();                    // "$1,000.00"
numerus(1000)->currency(in: 'EUR');           // "€1,000.00"
numerus(1000)->currency(in: 'GBP');           // "£1,000.00"
numerus(1234.56)->currency(precision: 2);     // "$1,234.56"

// With locale
numerus(1000)->currency(in: 'EUR', locale: 'de');  // "1.000,00 €"
numerus(1000)->currency(in: 'EUR', locale: 'fr');  // "1 000,00 €"
```

Configure default currency:

```php
Numerus::useCurrency('EUR');
numerus(1000)->currency();  // Uses EUR by default

Numerus::defaultCurrency();  // "EUR"
```

Temporary currency:

```php
$formatted = Numerus::withCurrency('GBP', function () {
    return numerus(1000)->currency();
});
// Uses GBP, then restores previous default
```

## Percentage Formatting

Format values as percentages:

```php
numerus(10)->percentage();          // "10%"
numerus(50)->percentage();          // "50%"
numerus(10.123)->percentage(2);     // "10.12%"
numerus(99.999)->percentage(1);     // "100.0%"

// With max precision
numerus(10.12)->percentage(0, 2);   // "10%"
```

## Ordinal Formatting

Format numbers with ordinal suffixes:

```php
numerus(1)->ordinal();    // "1st"
numerus(2)->ordinal();    // "2nd"
numerus(3)->ordinal();    // "3rd"
numerus(4)->ordinal();    // "4th"
numerus(11)->ordinal();   // "11th"
numerus(21)->ordinal();   // "21st"
numerus(101)->ordinal();  // "101st"
numerus(1000)->ordinal(); // "1,000th"
```

## Spelling Numbers

Convert numbers to word representations:

```php
numerus(1)->spell();      // "one"
numerus(10)->spell();     // "ten"
numerus(21)->spell();     // "twenty-one"
numerus(99)->spell();     // "ninety-nine"
numerus(100)->spell();    // "one hundred"
numerus(102)->spell();    // "one hundred two"

// With locale
numerus(42)->spell(locale: 'fr');  // "quarante-deux"
numerus(88)->spell(locale: 'de');  // "achtundachtzig"
```

Spell ordinals:

```php
numerus(1)->spellOrdinal();   // "first"
numerus(2)->spellOrdinal();   // "second"
numerus(3)->spellOrdinal();   // "third"
numerus(11)->spellOrdinal();  // "eleventh"
numerus(21)->spellOrdinal();  // "twenty-first"
```

## Trimming

Remove trailing zeros from decimal numbers:

```php
$num = numerus(12.0);
$trimmed = $num->trim();
// $trimmed->value() === 12.0

numerus(12.30)->trim()->value();   // 12.3
numerus(12.300)->trim()->value();  // 12.3
numerus(12.34)->trim()->value();   // 12.34
```

## Parsing

Parse locale-aware strings to Numerus instances:

```php
// Parse integers
$num = Numerus::parseInt('42');       // 42
$num = Numerus::parseInt('1,234');    // 1234

// Parse floats
$num = Numerus::parseFloat('42.5');      // 42.5
$num = Numerus::parseFloat('1,234.56');  // 1234.56

// With locale
$num = Numerus::parseFloat('1.234,56', locale: 'de');  // 1234.56
$num = Numerus::parseFloat('1 234,56', locale: 'fr');  // 1234.56
```

## Number Pairs

Generate arrays of number ranges:

```php
$pairs = Numerus::pairs(25, 10);
// [[1, 10], [11, 20], [21, 25]]

$pairs = Numerus::pairs(25, 10, offset: 0);
// [[0, 9], [10, 19], [20, 25]]

$pairs = Numerus::pairs(100, 25);
// [[1, 25], [26, 50], [51, 75], [76, 100]]
```

Useful for pagination:

```php
$totalRecords = 247;
$perPage = 50;
$ranges = Numerus::pairs($totalRecords, $perPage, offset: 0);
// [[0, 49], [50, 99], [100, 149], [150, 199], [200, 247]]

foreach ($ranges as [$start, $end]) {
    // Fetch records from $start to $end
}
```

## Locale Configuration

Set default locale for all formatting operations:

```php
Numerus::useLocale('fr');
numerus(1000)->format();  // "1 000"

Numerus::defaultLocale();  // "fr"
```

Temporarily use a different locale:

```php
$formatted = Numerus::withLocale('de', function () {
    return numerus(1234.56)->format(2);
});
// "1.234,56" (German format)

Numerus::defaultLocale();  // Returns to previous locale
```

## Practical Examples

### Format Price with Tax

```php
function formatPriceWithTax(float $price, float $taxRate): string
{
    return numerus($price)
        ->multiplyBy(1 + $taxRate)
        ->currency(in: 'USD', precision: 2);
}

$total = formatPriceWithTax(99.99, 0.08);  // "$107.99"
```

### Display File Upload Size

```php
function formatUploadSize(int $bytes): string
{
    return numerus($bytes)->fileSize(2);
}

$size = formatUploadSize(2048576);  // "1.95 MB"
```

### Format Statistics

```php
function formatStat(int $value): string
{
    if ($value >= 1000000) {
        return numerus($value)->abbreviate(1);
    }

    return numerus($value)->format();
}

$views = formatStat(1234567);   // "1.2M"
$likes = formatStat(999);        // "999"
```

### Progress Percentage

```php
function formatProgress(int $completed, int $total): string
{
    return numerus($completed)
        ->divideBy($total)
        ->multiplyBy(100)
        ->percentage(1);
}

$progress = formatProgress(75, 100);  // "75.0%"
```

### Format Contest Position

```php
function formatPosition(int $rank): string
{
    return numerus($rank)->spellOrdinal();
}

$position = formatPosition(1);   // "first"
$position = formatPosition(42);  // "forty-second"
```
