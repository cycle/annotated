<?php

declare(strict_types=1);

namespace Cycle\Annotated\Annotation;

use Cycle\Annotated\Annotation\Table\Index;
use Cycle\Annotated\Annotation\Table\PrimaryKey;
use Doctrine\Common\Annotations\Annotation\NamedArgumentConstructor;
use Doctrine\Common\Annotations\Annotation\Target;

/**
 * @Annotation
 * @NamedArgumentConstructor
 * @Target({"CLASS", "ANNOTATION"})
 */
#[\Attribute(\Attribute::TARGET_CLASS), NamedArgumentConstructor]
final class Table
{
    public function __construct(
        /** @var Column[] */
        private array $columns = [],
        /** @var PrimaryKey|null */
        private ?PrimaryKey $primary = null,
        /** @var Index[] */
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
