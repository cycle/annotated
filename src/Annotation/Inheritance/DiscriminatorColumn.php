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
    public function __construct(
        private string $name
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }
}
