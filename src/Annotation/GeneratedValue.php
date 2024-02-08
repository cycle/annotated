<?php

declare(strict_types=1);

namespace Cycle\Annotated\Annotation;

use Cycle\ORM\Schema\GeneratedField;
use Spiral\Attributes\NamedArgumentConstructor;

/**
 * @Annotation
 * @NamedArgumentConstructor
 * @Target({"PROPERTY"})
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
#[NamedArgumentConstructor]
class GeneratedValue
{
    public function __construct(
        protected bool $beforeInsert = false,
        protected bool $onInsert = false,
        protected bool $beforeUpdate = false,
    ) {
    }

    public function getFlags(): ?int
    {
        if (!$this->beforeInsert && !$this->onInsert && !$this->beforeUpdate) {
            return null;
        }

        return
            ($this->beforeInsert ? GeneratedField::BEFORE_INSERT : 0) |
            ($this->onInsert ? GeneratedField::ON_INSERT : 0) |
            ($this->beforeUpdate ? GeneratedField::BEFORE_UPDATE : 0);
    }
}
