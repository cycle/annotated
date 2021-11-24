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
        /**  @var non-empty-string|null */
        private ?string $role = null,
        /** @var class-string|null */
        private ?string $mapper = null,
        private string $columnPrefix = '',
        /** @var Column[] */
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
