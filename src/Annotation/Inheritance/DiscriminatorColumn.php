<?php

declare(strict_types=1);

namespace Cycle\Annotated\Annotation\Inheritance;

/**
 * @Annotation
 * @Target("CLASS")
 * @Attributes({
 *      @Attribute("name", type="string", required=true)
 * })
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
class DiscriminatorColumn
{
    protected string $name;

    /**
     * @param array<string, mixed> $values
     */
    public function __construct(array $values)
    {
        foreach ($values as $key => $value) {
            $this->$key = $value;
        }
    }

    public function getName(): string
    {
        return $this->name;
    }
}
