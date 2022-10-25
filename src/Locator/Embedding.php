<?php

declare(strict_types=1);

namespace Cycle\Annotated\Locator;

use Cycle\Annotated\Annotation\Embeddable;

final class Embedding
{
    public function __construct(
        public Embeddable $attribute,
        public \ReflectionClass $class
    ) {
    }
}
