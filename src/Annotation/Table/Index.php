<?php

declare(strict_types=1);

namespace Cycle\Annotated\Annotation\Table;

use Doctrine\Common\Annotations\Annotation\NamedArgumentConstructor;
use Doctrine\Common\Annotations\Annotation\Target;

/**
 * @Annotation
 * @NamedArgumentConstructor
 * @Target("ANNOTATION", "CLASS")
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::IS_REPEATABLE), NamedArgumentConstructor]
class Index
{
    public function __construct(
        /** @var non-empty-string[] */
        private array $columns,
        private bool $unique = false,
        /** @var non-empty-string|null */
        private ?string $name = null,
    ) {
    }

    public function getColumns(): array
    {
        return $this->columns;
    }

    public function getIndex(): ?string
    {
        return $this->name;
    }

    public function isUnique(): bool
    {
        return $this->unique;
    }
}
