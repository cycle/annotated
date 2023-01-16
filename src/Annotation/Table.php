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
final class Table
{
    /**
     * @param Column[] $columns
     * @param PrimaryKey|null $primary
     * @param Index[] $indexes
     */
    public function __construct(
        private array $columns = [],
        private ?PrimaryKey $primary = null,
        private array $indexes = [],
    ) {
    }

    public function getColumns(): array
    {
        return $this->columns;
    }

    public function getPrimary(): ?PrimaryKey
    {
        return $this->primary;
    }

    public function getIndexes(): array
    {
        return $this->indexes;
    }
}
