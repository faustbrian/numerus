<?php

declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use Cline\Numerus\Adapters\NativeMathAdapter;

$adapter = new NativeMathAdapter();

$result = $adapter->round(-2.5, 0, RoundingMode::PositiveInfinity);
echo "Adapter result: ";
var_dump($result);

$result2 = round(-2.5, 0, RoundingMode::PositiveInfinity);
echo "PHP native result: ";
var_dump($result2);
