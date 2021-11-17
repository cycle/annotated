<?php

declare(strict_types=1);

namespace Cycle\Annotated\Annotation;

abstract class Inheritance
{
    public function __construct(
        protected string $type
    ) {
    }

    public function getType(): string
    {
        return $this->type;
    }
}
