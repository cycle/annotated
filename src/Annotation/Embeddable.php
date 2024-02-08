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
final class Embeddable
{
    /**
     * @param non-empty-string|null $role Entity role. Defaults to the lowercase class name without a namespace.
     * @param class-string|null $mapper Mapper class name. Defaults to {@see \Cycle\ORM\Mapper\Mapper}.
     * @param string $columnPrefix Custom prefix for embeddable entity columns.
     * @param Column[] $columns Embedded entity columns.
     */
    public function __construct(
        private ?string $role = null,
        private ?string $mapper = null,
        private string $columnPrefix = '',
        private array $columns = [],
    ) {
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

    public function getColumns(): array
    {
        return $this->columns;
    }
}
