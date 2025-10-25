<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Cline\Numerus\Numerus;

use function Cline\Numerus\numerus;

describe('JsonSerializable', function (): void {
    describe('Happy Path', function (): void {
        test('serializes integer to JSON', function (): void {
            $num = numerus(42);

            expect(json_encode($num))->toBe('42');
        });

        test('serializes float to JSON', function (): void {
            $num = numerus(42.5);

            expect(json_encode($num))->toBe('42.5');
        });

        test('serializes negative integer to JSON', function (): void {
            $num = numerus(-100);

            expect(json_encode($num))->toBe('-100');
        });

        test('serializes zero to JSON', function (): void {
            $num = numerus(0);

            expect(json_encode($num))->toBe('0');
        });

        test('serializes in array to JSON', function (): void {
            $data = [
                'amount' => numerus(100),
                'tax' => numerus(20.5),
            ];

            expect(json_encode($data))->toBe('{"amount":100,"tax":20.5}');
        });

        test('serializes in object to JSON', function (): void {
            $obj = (object) [
                'price' => numerus(99.99),
                'quantity' => numerus(5),
            ];

            expect(json_encode($obj))->toBe('{"price":99.99,"quantity":5}');
        });

        test('serializes calculated result to JSON', function (): void {
            $num = numerus(100)->addPercent(20);

            expect(json_encode($num))->toBe('120');
        });

        test('serializes rounded value to JSON', function (): void {
            $num = numerus(42.555)->round(2);

            expect(json_encode($num))->toBe('42.56');
        });
    });

    describe('Edge Cases', function (): void {
        test('serializes very large integer to JSON', function (): void {
            $num = numerus(999_999_999_999);

            expect(json_encode($num))->toBe('999999999999');
        });

        test('serializes very small decimal to JSON', function (): void {
            $num = numerus(0.000_001);

            $result = json_encode($num);

            expect($result)->toBeString();
            expect((float) $result)->toBe(0.000_001);
        });

        test('serializes negative zero to JSON', function (): void {
            $num = numerus(-0.0);

            expect(json_encode($num))->toBe('0');
        });

        test('jsonSerialize returns native type', function (): void {
            $intNum = numerus(42);
            $floatNum = numerus(42.5);

            expect($intNum->jsonSerialize())->toBe(42);
            expect($floatNum->jsonSerialize())->toBe(42.5);
        });

        test('serializes trimmed value to JSON', function (): void {
            $num = numerus(42.500_0)->trim();

            expect(json_encode($num))->toBe('42.5');
        });

        test('serializes complex nested structure', function (): void {
            $data = [
                'invoice' => [
                    'subtotal' => numerus(100),
                    'tax' => numerus(20),
                    'total' => numerus(120),
                ],
                'items' => [
                    ['price' => numerus(50), 'qty' => numerus(2)],
                ],
            ];

            expect(json_encode($data))
                ->toBe('{"invoice":{"subtotal":100,"tax":20,"total":120},"items":[{"price":50,"qty":2}]}');
        });
    });
});
