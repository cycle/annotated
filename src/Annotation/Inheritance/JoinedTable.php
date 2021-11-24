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
class JoinedTable extends Inheritance
{
    public function __construct(
        private ?string $outerKey = null
    ) {
        parent::__construct('joined');
    }

    public function getOuterKey(): ?string
    {
        return $this->outerKey;
    }
}
