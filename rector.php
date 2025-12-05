<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Cline\CodingStandard\Rector\Factory;
use Rector\CodingStyle\Rector\FuncCall\ClosureFromCallableToFirstClassCallableRector;
use Rector\CodingStyle\Rector\FunctionLike\FunctionLikeToFirstClassCallableRector;
use Rector\DeadCode\Rector\Stmt\RemoveUnreachableStatementRector;

return Factory::create(
    paths: [__DIR__.'/src', __DIR__.'/tests'],
    skip: [
        RemoveUnreachableStatementRector::class => [__DIR__.'/tests'],
        // Skip first-class callable conversion in macro tests - $this context doesn't work
        FunctionLikeToFirstClassCallableRector::class => [
            __DIR__.'/tests/Unit/NumerusMacroableTest.php',
        ],
        ClosureFromCallableToFirstClassCallableRector::class => [
            __DIR__.'/tests/Unit/NumerusMacroableTest.php',
        ],
    ],
);
