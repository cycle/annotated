<?php

declare(strict_types=1);

namespace Cycle\Annotated\Annotation;

use Cycle\Annotated\Annotation\Table\Index;

final class Table
{
    /**
     * @param Column[] $columns
     * @param Index[] $indexes
     */
    public function __construct(
        array $columns = [],
        array $indexes = [],
    ) {
    }
}
