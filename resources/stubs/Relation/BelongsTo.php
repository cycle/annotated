<?php

declare(strict_types=1);

namespace Cycle\Annotated\Annotation\Relation;

use JetBrains\PhpStorm\ExpectedValues;

final class BelongsTo
{
    public function __construct(
        string $target,
        bool $cascade = true,
        bool $nullable = false,
        array|string $innerKey = '{relationName}_{outerKey}',
        array|string $outerKey = null,
        bool $fkCreate = true,
        #[ExpectedValues(values: ['NO ACTION', 'CASCADE', 'SET NULL'])]
        string $fkAction = 'CASCADE',
        bool $indexCreate = true,
        #[ExpectedValues(values: ['lazy', 'eager'])]
        string $load = 'lazy'
    ) {
    }
}
