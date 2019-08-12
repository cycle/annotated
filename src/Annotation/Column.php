<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */
declare(strict_types=1);

namespace Cycle\Annotated\Annotation;

use Doctrine\Common\Annotations\Annotation\Required;
use Doctrine\Common\Annotations\Annotation\Target;

/**
 * @Annotation
 * @Target("PROPERTY")
 */
final class Column
{
    /**
     * @Required()
     * @var string
     */
    public $type;

    /** @var bool */
    public $nullable = false;

    /** @var bool */
    public $primary = false;

    /** @var bool */
    public $hasDefault = false;

    /** @var bool */
    public $castDefault = false;

    /** @var string */
    public $name;

    /** @var mixed */
    public $default;

    /** @var mixed */
    public $typecast;

    /**
     * @inheritdoc
     */
    public function setAttribute(string $name, $value)
    {
        if ($name === "default") {
            $this->hasDefault = true;
        }

        parent::setAttribute($name, $value);
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
     * @return bool
     */
    public function isCastedDefault(): bool
    {
        return $this->castDefault;
    }

    /**
     * @return string|null
     */
    public function getColumn(): ?string
    {
        return $this->name;
    }

    /**
     * @return string|null
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * @return mixed|null
     */
    public function getTypecast()
    {
        return $this->typecast;
    }

    /**
     * @return mixed
     */
    public function getDefault()
    {
        return $this->default;
    }
}