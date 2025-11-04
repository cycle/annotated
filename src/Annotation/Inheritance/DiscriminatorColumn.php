<?php

declare(strict_types=1);

namespace Cycle\Annotated\Annotation\Inheritance;

use Spiral\Attributes\NamedArgumentConstructor;

/**
 * @Annotation
 * @NamedArgumentConstructor
 * @Target("CLASS")
 */
#[\Attribute(\Attribute::TARGET_CLASS), NamedArgumentConstructor]
class DiscriminatorColumn
{
    /**
     * @param non-empty-string $name
     */
    public function __construct(
        protected string $name,
    ) {}

    /**
     * @return non-empty-string
     */
    public function getName(): string
    {
        return $this->name;
    }
}
