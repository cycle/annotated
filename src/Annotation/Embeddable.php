<?php

declare(strict_types=1);

namespace Cycle\Annotated\Annotation;

use Doctrine\Common\Annotations\Annotation\Target;
use Spiral\Attributes\NamedArgumentConstructor;

/**
 * @Annotation
 * @NamedArgumentConstructor
 * @Target("CLASS")
 */
#[\Attribute(\Attribute::TARGET_CLASS), NamedArgumentConstructor]
class Embeddable
{
    /**
     * @param non-empty-string|null $role Entity role. Defaults to the lowercase class name without a namespace.
     * @param class-string|null $mapper Mapper class name. Defaults to {@see \Cycle\ORM\Mapper\Mapper}.
     * @param string $columnPrefix Custom prefix for embeddable entity columns.
     * @param Column[] $columns Embedded entity columns.
     */
    public function __construct(
        protected ?string $role = null,
        protected ?string $mapper = null,
        protected string $columnPrefix = '',
        protected array $columns = [],
    ) {
    }

    /**
     * @return non-empty-string|null
     */
    public function getRole(): ?string
    {
        return $this->role;
    }

    /**
     * @return class-string|null
     */
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
