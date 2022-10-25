<?php

declare(strict_types=1);

namespace Cycle\Annotated\Locator;

use Cycle\Annotated\Annotation\Entity as Attribute;

final class Entity
{
    public function __construct(
        public Attribute $attribute,
        public \ReflectionClass $class
    ) {
    }
}
