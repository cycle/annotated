<?php

declare(strict_types=1);

namespace Cycle\Annotated\Annotation\Relation\Morphed;

final class BelongsToMorphed
{
    public function __construct(
        string $target,
        bool $cascade = true,
        bool $nullable = true,
        array|string $innerKey = '{relationName}_{outerKey}',
        array|string $outerKey = null,
        string $morphKey = '{relationName}_role',
        int $morphKeyLength = 32,
        bool $indexCreate = true
    ) {
    }
}
