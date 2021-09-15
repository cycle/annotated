<?php

declare(strict_types=1);

namespace Cycle\Annotated\Annotation;

use Doctrine\Common\Annotations\Annotation\Attribute;
use Doctrine\Common\Annotations\Annotation\Attributes;
use Doctrine\Common\Annotations\Annotation\Target;

/**
 * @Annotation
 * @Target({"PROPERTY", "ANNOTATION", "CLASS"})
 * @Attributes({
 *      @Attribute("type", type="string", required=true),
 *      @Attribute("name", type="string"),
 *      @Attribute("property", type="string"),
 *      @Attribute("primary", type="bool"),
 *      @Attribute("nullable", type="bool"),
 *      @Attribute("default", type="mixed"),
 *      @Attribute("typecast", type="mixed"),
 * })
 */
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_CLASS | \Attribute::IS_REPEATABLE)]
final class Column
{
    private bool $hasDefault = false;

    private ?string $name = null;

    private ?string $property = null;

    private ?string $type = null;

    private bool $nullable = false;

    private bool $primary = false;

    private mixed $default = null;

    private bool $castDefault = false;

    private mixed $typecast = null;

    /**
     * @param array<string, mixed> $values
     */
    public function __construct(array $values)
    {
        if (isset($values['default'])) {
            $this->hasDefault = true;
        }

        foreach ($values as $key => $value) {
            $this->$key = $value;
        }
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function getColumn(): ?string
    {
        return $this->name;
    }

    public function getProperty(): ?string
    {
        return $this->property;
    }

    public function isNullable(): bool
    {
        return $this->nullable;
    }

    public function isPrimary(): bool
    {
        return $this->primary;
    }

    public function hasDefault(): bool
    {
        return $this->hasDefault;
    }

    public function getDefault(): mixed
    {
        return $this->default;
    }

    public function castDefault(): bool
    {
        return $this->castDefault;
    }

    public function getTypecast(): mixed
    {
        return $this->typecast;
    }
}
