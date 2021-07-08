<?php

declare(strict_types=1);

namespace Cycle\Annotated\Annotation;

use Cycle\Annotated\Annotation\Table\Index;
use Cycle\Annotated\Annotation\Table\PrimaryKey;
use Doctrine\Common\Annotations\Annotation\Attribute;
use Doctrine\Common\Annotations\Annotation\Attributes;
use Doctrine\Common\Annotations\Annotation\Target;

/**
 * @Annotation
 * @Target({"CLASS", "ANNOTATION"})
 * @Attributes({
 *      @Attribute("columns", type="array<Cycle\Annotated\Annotation\Column>"),
 *      @Attribute("primary", type="Cycle\Annotated\Annotation\Table\PrimaryKey"),
 *      @Attribute("indexes", type="array<Cycle\Annotated\Annotation\Table\Index>"),
 * })
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
final class Table
{
    /** @var array<Column> */
    private $columns = [];

    /** @var PrimaryKey */
    private $primary = null;

    /** @var array<Index> */
    private $indexes = [];

    /**
     * @param array $values
     */
    public function __construct(array $values)
    {
        foreach ($values as $key => $value) {
            $this->$key = $value;
        }
    }

    /**
     * @return Column[]
     */
    public function getColumns(): array
    {
        return $this->columns;
    }

    /**
     * @return ?PrimaryKey
     */
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
