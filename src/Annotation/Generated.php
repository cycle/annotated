<?php

declare(strict_types=1);

namespace Cycle\Annotated\Annotation;

use Cycle\Annotated\Enum\GeneratedType;
use Spiral\Attributes\NamedArgumentConstructor;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
#[NamedArgumentConstructor]
class Generated
{
    protected int $type = 0;

    /**
     * @param GeneratedType|int ...$type Generating type {@see GeneratedType}.
     */
    public function __construct(GeneratedType|int ...$type)
    {
        foreach ($type as $value) {
            $this->type |= $value instanceof GeneratedType ? $value->value : $value;
        }
    }

    public function getType(): int
    {
        return $this->type;
    }
}
