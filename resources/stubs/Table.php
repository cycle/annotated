<?php

declare(strict_types=1);

namespace Cycle\Annotated\Annotation;

use Cycle\Annotated\Annotation\Table\Index;
use Cycle\Annotated\Annotation\Table\PrimaryKey;

final class Table
{
    /**
     * @param Column[] $columns
     * @param ?PrimaryKey $primary
     * @param Index[] $indexes
     */
    public function __construct(
        array $columns = [],
        $primary = null,
        array $indexes = []
    ) {
    }
}
