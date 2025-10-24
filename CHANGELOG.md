# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added
- Immutable numeric value object with `create()` factory method
- `numerus()` namespaced helper function for convenient instance creation
- Arithmetic operations: `plus()`, `minus()`, `multiplyBy()`, `divideBy()`, `mod()`, `power()`, `sqrt()`
- Rounding operations: `abs()`, `ceil()`, `floor()`, `round()`, `negate()`
- Comparison operations: `equals()`, `greaterThan()`, `greaterThanOrEqual()`, `lessThan()`, `lessThanOrEqual()`
- Min/max operations: `min()`, `max()`, `clamp()`
- Type checking: `isPositive()`, `isNegative()`, `isZero()`, `isEven()`, `isOdd()`
- Type conversions: `toInt()`, `toFloat()`, `toString()`, `__toString()`
- Laravel Number formatting methods:
  - `abbreviate()` - Format as "1K", "1.23M", "1B", "1T"
  - `forHumans()` - Format as "1 thousand", "1.23 million"
  - `fileSize()` - Format bytes as "1 KB", "1 MB", "1 GB"
  - `format()` - Format with thousands separators
  - `currency()` - Format as currency with locale support
  - `percentage()` - Format as percentage
  - `ordinal()` - Format as "1st", "2nd", "3rd"
  - `spell()` - Spell out "one hundred"
  - `spellOrdinal()` - Spell out "first", "second"
  - `trim()` - Remove trailing zeros
- Static configuration methods: `useLocale()`, `useCurrency()`, `defaultLocale()`, `defaultCurrency()`
- Static parsing methods: `parseInt()`, `parseFloat()`, `pairs()`
- Locale and currency context methods: `withLocale()`, `withCurrency()`
- Full internationalization support via ext-intl
- Comprehensive test suite with 100% coverage
- Cookbook documentation with practical examples
