<?php

declare(strict_types=1);

namespace Cycle\Annotated\Annotation\Table;

use Doctrine\Common\Annotations\Annotation\Target;
use Spiral\Attributes\NamedArgumentConstructor;

/**
 * @Annotation
 * @NamedArgumentConstructor
 * @Target("ANNOTATION", "CLASS")
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::IS_REPEATABLE), NamedArgumentConstructor]
class Index
{
    /**
     * @param non-empty-string[] $columns
     * @param non-empty-string|null $name
     */
    public function __construct(
        private array $columns,
        private bool $unique = false,
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
