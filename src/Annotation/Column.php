<?php

/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

declare(strict_types=1);

namespace Cycle\Annotated\Annotation;

use Doctrine\Common\Annotations\Annotation\Attribute;
use Doctrine\Common\Annotations\Annotation\Attributes;
use Doctrine\Common\Annotations\Annotation\Target;

/**
 * @Annotation
 * @Target({"PROPERTY", "ANNOTATION"})
 * @Attributes({
 *      @Attribute("type", type="string", required=true),
 *      @Attribute("name", type="string"),
 *      @Attribute("primary", type="bool"),
 *      @Attribute("nullable", type="bool"),
 *      @Attribute("default", type="mixed"),
 *      @Attribute("typecast", type="mixed"),
 * })
 */
final class Column
{
    /** @var bool */
    private $hasDefault = false;

    /** @var string */
    private $name;

    /** @var string */
    private $type;

    /** @var bool */
    private $nullable = false;

    /** @var bool */
    private $primary = false;

    /** @var mixed */
    private $default;

    /** @var bool */
    private $castDefault = false;

    /** @var mixed */
    private $typecast;

    /**
     * @param array $values
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

    /**
     * @return string|null
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * @return string|null
     */
    public function getColumn(): ?string
    {
        return $this->name;
    }

    /**
     * @return bool
     */
    public function isNullable(): bool
    {
        return $this->nullable;
    }

    /**
     * @return bool
     */
    public function isPrimary(): bool
    {
        return $this->primary;
    }

    /**
     * @return bool
     */
    public function hasDefault(): bool
    {
        return $this->hasDefault;
    }

    /**
     * @return mixed
     */
    public function getDefault()
    {
        return $this->default;
    }

    /**
     * @return bool
     */
    public function castDefault(): bool
    {
        return $this->castDefault;
    }

    /**
     * @return mixed|null
     */
    public function getTypecast()
    {
        return $this->typecast;
    }
}
