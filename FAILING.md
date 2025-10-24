# Failing Tests (78/440 = 17.7% failure rate)

## Summary
**Total Tests:** 440
**Passing:** 362 (82.3%)
**Failing:** 78 (17.7%)

## Analysis

### Category Breakdown

#### 1. Square Root Operations (3 failures)
- [ ] Arithmetic Operations → Happy Path → calculates square root
- [ ] Arithmetic Operations → Edge Cases → calculates square root of zero
- [ ] Arithmetic Operations → Edge Cases → calculates square root of one

**Issue:** Tests expect `float` type (e.g., `4.0`) but adapter returns normalized type (e.g., `4` as int).
**Root Cause:** `sqrt()` in Numerus.php:726 was changed to not normalize but still fails.

---

#### 2. Rounding Operations (62 failures)
All rounding-related tests are failing with type mismatches.

**Generic Rounding (9 failures)**
- [ ] Rounding Operations → Happy Path → rounds to nearest integer
- [ ] Rounding Operations → Happy Path → rounds with precision
- [ ] Rounding Operations → Edge Cases → rounds integer
- [ ] Rounding Operations → Edge Cases → rounds exactly halfway up
- [ ] Rounding Operations → Edge Cases → rounds exactly halfway down
- [ ] Rounding Operations → Edge Cases → rounds negative number
- [ ] Rounding Operations → Edge Cases → rounds with zero precision
- [ ] Rounding Operations → Edge Cases → rounds with high precision
- [ ] Immutability → chains multiple rounding operations

**AwayFromZero Mode (6 failures)**
- [ ] RoundingMode → AwayFromZero → Happy Path → rounds away from zero for positive numbers
- [ ] RoundingMode → AwayFromZero → Happy Path → rounds away from zero for negative numbers
- [ ] RoundingMode → AwayFromZero → Happy Path → rounds with precision
- [ ] RoundingMode → AwayFromZero → Edge Cases → rounds zero
- [ ] RoundingMode → AwayFromZero → Edge Cases → rounds exactly halfway
- [ ] RoundingMode → AwayFromZero → Edge Cases → rounds integer unchanged

**TowardsZero Mode (6 failures)**
- [ ] RoundingMode → TowardsZero → Happy Path → rounds towards zero for positive numbers
- [ ] RoundingMode → TowardsZero → Happy Path → rounds towards zero for negative numbers
- [ ] RoundingMode → TowardsZero → Happy Path → rounds with precision
- [ ] RoundingMode → TowardsZero → Edge Cases → rounds zero
- [ ] RoundingMode → TowardsZero → Edge Cases → rounds exactly halfway
- [ ] RoundingMode → TowardsZero → Edge Cases → rounds integer unchanged

**PositiveInfinity Mode (6 failures)**
- [ ] RoundingMode → PositiveInfinity → Happy Path → rounds towards positive infinity for positive numbers
- [ ] RoundingMode → PositiveInfinity → Happy Path → rounds towards positive infinity for negative numbers
- [ ] RoundingMode → PositiveInfinity → Happy Path → rounds with precision
- [ ] RoundingMode → PositiveInfinity → Edge Cases → rounds zero
- [ ] RoundingMode → PositiveInfinity → Edge Cases → rounds exactly halfway
- [ ] RoundingMode → PositiveInfinity → Edge Cases → rounds integer unchanged

**NegativeInfinity Mode (6 failures)**
- [ ] RoundingMode → NegativeInfinity → Happy Path → rounds towards negative infinity for positive numbers
- [ ] RoundingMode → NegativeInfinity → Happy Path → rounds towards negative infinity for negative numbers
- [ ] RoundingMode → NegativeInfinity → Happy Path → rounds with precision
- [ ] RoundingMode → NegativeInfinity → Edge Cases → rounds zero
- [ ] RoundingMode → NegativeInfinity → Edge Cases → rounds exactly halfway
- [ ] RoundingMode → NegativeInfinity → Edge Cases → rounds integer unchanged

**HalfAwayFromZero Mode (6 failures)**
- [ ] RoundingMode → HalfAwayFromZero → Happy Path → rounds to nearest neighbor
- [ ] RoundingMode → HalfAwayFromZero → Happy Path → rounds ties away from zero
- [ ] RoundingMode → HalfAwayFromZero → Happy Path → rounds with precision
- [ ] RoundingMode → HalfAwayFromZero → Edge Cases → rounds zero
- [ ] RoundingMode → HalfAwayFromZero → Edge Cases → rounds integer unchanged
- [ ] RoundingMode → HalfAwayFromZero → Edge Cases → rounds 1.5 and 3.5

**HalfTowardsZero Mode (6 failures)**
- [ ] RoundingMode → HalfTowardsZero → Happy Path → rounds to nearest neighbor
- [ ] RoundingMode → HalfTowardsZero → Happy Path → rounds ties towards zero
- [ ] RoundingMode → HalfTowardsZero → Happy Path → rounds with precision
- [ ] RoundingMode → HalfTowardsZero → Edge Cases → rounds zero
- [ ] RoundingMode → HalfTowardsZero → Edge Cases → rounds integer unchanged
- [ ] RoundingMode → HalfTowardsZero → Edge Cases → rounds 1.5 and 3.5

**HalfEven (Banker's) Mode (6 failures)**
- [ ] RoundingMode → HalfEven (Bankers Rounding) → Happy Path → rounds to nearest neighbor
- [ ] RoundingMode → HalfEven (Bankers Rounding) → Happy Path → rounds ties to even
- [ ] RoundingMode → HalfEven (Bankers Rounding) → Happy Path → rounds with precision
- [ ] RoundingMode → HalfEven (Bankers Rounding) → Edge Cases → rounds zero
- [ ] RoundingMode → HalfEven (Bankers Rounding) → Edge Cases → rounds integer unchanged
- [ ] RoundingMode → HalfEven (Bankers Rounding) → Edge Cases → rounds negative ties to even
- [ ] RoundingMode → HalfEven (Bankers Rounding) → Edge Cases → rounds 0.5 and 1.5

**HalfOdd Mode (6 failures)**
- [ ] RoundingMode → HalfOdd → Happy Path → rounds to nearest neighbor
- [ ] RoundingMode → HalfOdd → Happy Path → rounds ties to odd
- [ ] RoundingMode → HalfOdd → Edge Cases → rounds zero
- [ ] RoundingMode → HalfOdd → Edge Cases → rounds integer unchanged
- [ ] RoundingMode → HalfOdd → Edge Cases → rounds negative ties to odd
- [ ] RoundingMode → HalfOdd → Edge Cases → rounds 0.5 and 1.5

**Convenience Methods (11 failures)**
- [ ] RoundingMode → Convenience Methods → Happy Path → roundAwayFromZero convenience method
- [ ] RoundingMode → Convenience Methods → Happy Path → roundTowardsZero convenience method
- [ ] RoundingMode → Convenience Methods → Happy Path → roundPositiveInfinity convenience method
- [ ] RoundingMode → Convenience Methods → Happy Path → roundNegativeInfinity convenience method
- [ ] RoundingMode → Convenience Methods → Happy Path → roundHalfAwayFromZero convenience method
- [ ] RoundingMode → Convenience Methods → Happy Path → roundHalfTowardsZero convenience method
- [ ] RoundingMode → Convenience Methods → Happy Path → roundHalfEven convenience method
- [ ] RoundingMode → Convenience Methods → Happy Path → roundHalfOdd convenience method
- [ ] RoundingMode → Convenience Methods → Edge Cases → convenience methods handle zero
- [ ] RoundingMode → Convenience Methods → Edge Cases → convenience methods handle integers
- [ ] RoundingMode → Convenience Methods → Edge Cases → convenience methods preserve immutability

**Default Mode (1 failure)**
- [ ] RoundingMode → Default Mode → defaults to HalfAwayFromZero when no mode specified

**Immutability (1 failure)**
- [ ] RoundingMode → Immutability → maintains immutability

**Issue:** All rounding methods return `float` from native PHP `round()`, but tests expect exact float values. The `normalize()` helper converts `43.0` → `43` (int), breaking tests.
**Root Cause:** NativeMathAdapter needs to preserve return types from native PHP functions.

---

#### 3. Helper Function Edge Cases (3 failures)
- [ ] numerus() Helper Function → Edge Cases → allows complex chaining operations
- [ ] numerus() Helper Function → Edge Cases → can use with mathematical operations
- [ ] numerus() Helper Function → Edge Cases → can use with rounding operations

**Issue:** Likely cascading failures from rounding/sqrt issues above.

---

#### 4. Large Number Operations (1 failure)
- [ ] Arithmetic Operations → Edge Cases → adds very large numbers

**Issue:** Possible float precision issue or type normalization problem with very large values.

---

## Root Cause Analysis

**Primary Issue:** Type normalization in `Numerus::normalize()` (line 58-67)

The `normalize()` function converts floats that are whole numbers (e.g., `4.0`, `43.0`) into integers. However:
1. Native PHP `round()` always returns `float`, even for precision=0
2. Native PHP `sqrt()` always returns `float`
3. Tests expect these functions to preserve float types

**Current normalize() logic:**
```php
private static function normalize(int|float|string $value): int|float
{
    if (\is_string($value)) {
        $floatValue = (float) $value;
        return floor($floatValue) == $floatValue ? (int) $floatValue : $floatValue;
    }
    return $value;
}
```

**Problem:** When Math facade returns `4.0` (float from sqrt), and we normalize it, it becomes `4` (int), but tests expect `4.0`.

---

## Recommendations

1. **Remove `normalize()` from operations that should always return float:**
   - `sqrt()` - already done but still failing
   - All `round*()` methods - need verification

2. **Check NativeMathAdapter return types:**
   - Ensure `round()` returns the same type as native PHP `round()`
   - Ensure `sqrt()` returns the same type as native PHP `sqrt()`

3. **Investigate test expectations:**
   - Verify tests are checking for correct types based on native PHP behavior
   - Some tests may be overly strict on type comparisons

4. **Consider adapter-specific type handling:**
   - Native adapter should match native PHP exactly
   - BCMath/GMP adapters may need different type rules
