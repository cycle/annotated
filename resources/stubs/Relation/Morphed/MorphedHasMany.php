<?php

declare(strict_types=1);

namespace Cycle\Annotated\Annotation\Relation\Morphed;

use JetBrains\PhpStorm\ExpectedValues;

final class MorphedHasMany
{
    public function __construct(
        string $target,
        bool $cascade = true,
        bool $nullable = false,
        string $innerKey = null,
        string $outerKey = '{parentRole}_{innerKey}',
        string $morphKey = '{relationName}_role',
        int $morphKeyLength = 32,
        array $where = [],
        bool $indexCreate = true,
        #[ExpectedValues(values: ['lazy', 'eager'])]
        string $load = 'lazy',
        // Inverse $inverse = null, // can be uncommented for compatibility with php 8.1
    ) {
    }
}
