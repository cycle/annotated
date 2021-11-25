<?php

declare(strict_types=1);

namespace Cycle\Annotated\Annotation\Inheritance;

use Cycle\Annotated\Annotation\Inheritance;
use Doctrine\Common\Annotations\Annotation\NamedArgumentConstructor;

/**
 * @Annotation
 * @NamedArgumentConstructor
 * @Target("CLASS")
 */
#[\Attribute(\Attribute::TARGET_CLASS), NamedArgumentConstructor]
class SingleTable extends Inheritance
{
    public function __construct(
        private ?string $value = null
    ) {
        parent::__construct('single');
    }

    public function getValue(): ?string
    {
        return $this->value;
    }
}
