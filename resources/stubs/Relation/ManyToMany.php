<?php

declare(strict_types=1);

namespace Cycle\Annotated\Annotation\Relation;

use JetBrains\PhpStorm\ExpectedValues;

final class ManyToMany
{
    public function __construct(
        string $target,
        string $though = null,
        string $through = null,
        bool $cascade = true,
        bool $nullable = false,
        array|string $innerKey = null,
        array|string $outerKey = '{parentRole}_{innerKey}',
        array $where = [],
        array $orderBy = [],
        array|string $thoughInnerKey = '{sourceRole}_{innerKey}',
        array|string $thoughOuterKey = '{targetRole}_{outerKey}',
        array $thoughWhere = [],
        array|string $throughInnerKey = [],
        array|string $throughOuterKey = [],
        array $throughWhere = [],
        bool $fkCreate = true,
        #[ExpectedValues(values: ['NO ACTION', 'CASCADE', 'SET NULL'])]
        string $fkAction = 'SET NULL',
        bool $indexCreate = true,
        #[ExpectedValues(values: ['lazy', 'eager'])]
        string $load = 'lazy'
    ) {
    }
}
