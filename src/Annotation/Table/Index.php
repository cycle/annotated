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
        protected array $columns,
        protected bool $unique = false,
        protected ?string $name = null,
    ) {}

    /**
     * @return non-empty-string[]
     */
    public function getColumns(): array
    {
        return $this->columns;
    }

    /**
     * @return non-empty-string|null
     */
    public function getIndex(): ?string
    {
        return $this->name;
    }

    public function isUnique(): bool
    {
        return $this->unique;
    }
}
