<?php

declare(strict_types=1);

namespace Cycle\Annotated\Annotation\Relation\Morphed;

use JetBrains\PhpStorm\ExpectedValues;

final class MorphedHasOne
{
    public function __construct(
        string $target,
        bool $cascade = true,
        bool $nullable = false,
        array|string $innerKey = null,
        array|string $outerKey = '{parentRole}_{innerKey}',
        string $morphKey = '{relationName}_role',
        int $morphKeyLength = 32,
        bool $indexCreate = true,
        #[ExpectedValues(values: ['lazy', 'eager'])]
        string $load = 'lazy'
    ) {
    }
}
