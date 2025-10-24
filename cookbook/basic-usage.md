# Basic Usage

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
