<?php

declare(strict_types=1);

namespace Cycle\Annotated\Annotation;

use Cycle\Annotated\Annotation\Table\Index;
use Cycle\Annotated\Annotation\Table\PrimaryKey;
use Doctrine\Common\Annotations\Annotation\Target;
use Spiral\Attributes\NamedArgumentConstructor;

/**
 * @Annotation
 * @NamedArgumentConstructor
 * @Target({"CLASS", "ANNOTATION"})
 */
#[\Attribute(\Attribute::TARGET_CLASS), NamedArgumentConstructor]
class Table
{
    /**
     * @param Column[] $columns
     * @param Index[] $indexes
     */
    public function __construct(
        protected array $columns = [],
        protected ?PrimaryKey $primary = null,
        protected array $indexes = [],
    ) {}

    /**
     * @return Column[]
     */
    public function getColumns(): array
    {
        return $this->columns;
    }

    public function getPrimary(): ?PrimaryKey
    {
        return $this->primary;
    }

    /**
     * @return Index[]
     */
    public function getIndexes(): array
    {
        return $this->indexes;
    }
}
