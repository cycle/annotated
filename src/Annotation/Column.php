<?php declare(strict_types=1);
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Cycle\Annotated\Annotation;


use Spiral\Annotations\AbstractAnnotation;
use Spiral\Annotations\Parser;

final class Column extends AbstractAnnotation
{
    public const NAME   = 'column';
    public const SCHEMA = [
        'name'        => Parser::STRING,
        'type'        => Parser::STRING,
        'primary'     => Parser::BOOL,
        'typecast'    => Parser::MIXED,
        'nullable'    => Parser::BOOL,
        'default'     => Parser::MIXED,
        'castDefault' => Parser::BOOL
    ];

    /** @var bool */
    protected $nullable = false;

    /** @var bool */
    protected $primary = false;

    /** @var bool */
    protected $hasDefault = false;

    /** @var bool */
    protected $castDefault = false;

    /** @var string|null */
    protected $name;

    /** @var string|null */
    protected $type;

    /** @var mixed */
    protected $default;

    /** @var mixed|null */
    protected $typecast;

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