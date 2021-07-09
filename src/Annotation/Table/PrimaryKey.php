<?php

declare(strict_types=1);

namespace Cycle\Annotated\Annotation\Table;

use Doctrine\Common\Annotations\Annotation\Attribute;
use Doctrine\Common\Annotations\Annotation\Attributes;
use Doctrine\Common\Annotations\Annotation\Target;

/**
 * @Annotation
 * @Target("ANNOTATION", "CLASS")
 * @Attributes({
 *      @Attribute("columns", type="array<string>", required=true),
 * })
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
class PrimaryKey extends Index
{
    protected $unique = true;

    public function __construct(array $values)
    {
        foreach ($values as $key => $value) {
            if ($key === 'unique') {
                continue;
            }

            $this->$key = $value;
        }
    }
}
