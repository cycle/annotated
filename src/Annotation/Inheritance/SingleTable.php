<?php

declare(strict_types=1);

namespace Cycle\Annotated\Annotation\Inheritance;

use Cycle\Annotated\Annotation\Inheritance;
use Spiral\Attributes\NamedArgumentConstructor;

/**
 * @Annotation
 * @NamedArgumentConstructor
 * @Target("CLASS")
 */
#[\Attribute(\Attribute::TARGET_CLASS), NamedArgumentConstructor]
class SingleTable extends Inheritance
{
    protected ?string $value;

    public function __construct(
        string|int|float|\Stringable|\BackedEnum|null $value = null,
    ) {
        $this->value = $value === null
            ? null
            : (string) ($value instanceof \BackedEnum ? $value->value : $value);
        parent::__construct('single');
    }

    public function getValue(): ?string
    {
        return $this->value;
    }
}
