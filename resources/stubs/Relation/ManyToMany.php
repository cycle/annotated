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
        string $innerKey = null,
        string $outerKey = '{parentRole}_{innerKey}',
        array $where = [],
        array $orderBy = [],
        string $thoughInnerKey = '{sourceRole}_{innerKey}',
        string $thoughOuterKey = '{targetRole}_{outerKey}',
        array $thoughWhere = [],
        array $throughInnerKey = [],
        array $throughOuterKey = [],
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
