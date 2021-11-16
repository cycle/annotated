<?php

declare(strict_types=1);

namespace Cycle\Annotated\Annotation\Inheritance;

use Cycle\Annotated\Annotation\Inheritance;

/**
 * @Annotation
 * @Target("CLASS")
 * @Attributes({
 *      @Attribute("value", type="string")
 * })
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
class SingleTable extends Inheritance
{
    protected string $type = 'single';

    protected ?string $value = null;

    public function getValue(): ?string
    {
        return $this->value;
    }
}
