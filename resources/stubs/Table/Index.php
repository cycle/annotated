<?php

declare(strict_types=1);

namespace Cycle\Annotated\Annotation\Table;

class Index
{
    public function __construct(
        string $name,
        array $columns = [],
        bool $unique = false,
    ) {
    }
}
