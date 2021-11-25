<?php

declare(strict_types=1);

namespace Cycle\Annotated\Annotation;

use Doctrine\Common\Annotations\Annotation\NamedArgumentConstructor;
use Doctrine\Common\Annotations\Annotation\Target;

/**
 * @Annotation
 * @NamedArgumentConstructor
 * @Target("CLASS")
 */
#[\Attribute(\Attribute::TARGET_CLASS), NamedArgumentConstructor]
final class Embeddable
{
    public function __construct(
        /**
         * Entity role. Defaults to the lowercase class name without a namespace
         *
         * @var non-empty-string|null
         */
        private ?string $role = null,
        /**
         * Mapper class name. Defaults to Cycle\ORM\Mapper\Mapper
         *
         * @var class-string|null
         */
        private ?string $mapper = null,
        /**
         * Сustom prefix for rmbeddable entity columns
         */
        private string $columnPrefix = '',
        /**
         * Embedded entity columns.
         *
         * @var Column[]
         */
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
