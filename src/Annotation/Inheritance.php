<?php

declare(strict_types=1);

namespace Cycle\Annotated\Annotation;

use Doctrine\Common\Annotations\Annotation\Attribute;
use Doctrine\Common\Annotations\Annotation\Attributes;
use Doctrine\Common\Annotations\Annotation\Target;

/**
 * @Annotation
 * @Target("CLASS")
 * @Attributes({
 *      @Attribute("type", type="string", required=true)
 * })
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
abstract class Inheritance
{
    /**
     * @Required()
     * @Enum({"single", "joined"}
     */
    protected string $type;

    /**
     * @param array<string, mixed> $values
     */
    public function __construct(array $values)
    {
        foreach ($values as $key => $value) {
            $this->$key = $value;
        }
    }

    public function getType(): string
    {
        return $this->type;
    }
}
