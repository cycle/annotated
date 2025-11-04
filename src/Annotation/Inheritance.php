<?php

declare(strict_types=1);

namespace Cycle\Annotated\Annotation;

use Doctrine\Common\Annotations\Annotation\Required;

abstract class Inheritance
{
    public function __construct(
        /** @Required() */
        protected string $type,
    ) {}

    public function getType(): string
    {
        return $this->type;
    }
}
