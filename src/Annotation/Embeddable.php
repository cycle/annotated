<?php

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
#[\Attribute(\Attribute::TARGET_CLASS)]
final class Embeddable
{
    private ?string $role = null;

    private ?string $mapper = null;

    private string $columnPrefix = '';

    /** @var Column[] */
    private $columns = [];

    /**
     * @param array<string, mixed> $values
     */
    public function __construct(array $values)
    {
        foreach ($values as $key => $value) {
            $this->$key = $value;
        }
    }

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function getMapper(): ?string
    {
        return $this->mapper;
    }

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
