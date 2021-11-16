<?php

declare(strict_types=1);

namespace Cycle\Annotated\Annotation\Inheritance;

use Cycle\Annotated\Annotation\Inheritance;

/**
 * @Annotation
 * @Target("CLASS")
 * @Attributes({
 *      @Attribute("outerKey", type="string")
 * })
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
class JoinedTable extends Inheritance
{
    protected string $type = 'joined';

    protected ?string $outerKey = null;

    public function getOuterKey(): ?string
    {
        return $this->outerKey;
    }
}
