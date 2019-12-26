<?php

/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

declare(strict_types=1);

namespace Cycle\Annotated\Annotation;

use Doctrine\Common\Annotations\Annotation\Attribute;
use Doctrine\Common\Annotations\Annotation\Attributes;
use Doctrine\Common\Annotations\Annotation\Target;

/**
 * @Annotation
 * @Target("CLASS")
 * @Attributes({
 *      @Attribute("role", type="string"),
 *      @Attribute("mapper", type="string"),
 *      @Attribute("columnPrefix", type="string"),
 *      @Attribute("columns", type="array<Cycle\Annotated\Annotation\Column>"),
 * })
 */
final class Embeddable
{
    /** @var string */
    private $role;

    /** @var string */
    private $mapper;

    /** @var string */
    private $columnPrefix = '';

    /** @var array<Column> */
    private $columns = [];

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
     * @return string|null
     */
    public function getRole(): ?string
    {
        return $this->role;
    }

    /**
     * @return string|null
     */
    public function getMapper(): ?string
    {
        return $this->mapper;
    }

    /**
     * @return string
     */
    public function getColumnPrefix(): string
    {
        return $this->columnPrefix;
    }

    /**
     * @return Column[]
     */
    public function getColumns(): array
    {
        return $this->columns;
    }
}
