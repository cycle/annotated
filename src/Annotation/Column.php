<?php

declare(strict_types=1);

namespace Cycle\Annotated\Annotation;

use Cycle\ORM\Parser\Typecast;
use Doctrine\Common\Annotations\Annotation\NamedArgumentConstructor;
use Doctrine\Common\Annotations\Annotation\Target;
use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\ExpectedValues;

/**
 * @Annotation
 * @NamedArgumentConstructor
 * @Target({"PROPERTY", "ANNOTATION", "CLASS"})
 */
#[
    \Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_CLASS | \Attribute::IS_REPEATABLE),
    NamedArgumentConstructor
]
final class Column
{
    private bool $hasDefault = false;

    /**
     * @param non-empty-string $type Column type. {@see \Cycle\Database\Schema\AbstractColumn::$mapping}
     * @param non-empty-string|null $name Column name. Defaults to the property name.
     * @param non-empty-string|null $property Property that belongs to column. For virtual columns.
     * @param bool $primary Explicitly set column as a primary key.
     * @param bool $nullable Set column as nullable.
     * @param mixed|null $default Default column value.
     * @param non-empty-string|null $typecast Typecast rule name.
     *        Regarding the default Typecast handler {@see Typecast} the value can be `callable` or
     *        one of ("int"|"float"|"bool"|"datetime") based on column type.
     *        If you want to use another rule you should add in the `typecast` argument of the {@see Entity} attribute
     *        a relevant Typecast handler that supports the rule.
     * @param bool $castDefault
     */
    public function __construct(
        #[ExpectedValues(values: ['primary', 'bigPrimary', 'enum', 'boolean', 'integer', 'tinyInteger', 'bigInteger',
            'string', 'text', 'tinyText', 'longText', 'double', 'float', 'decimal', 'datetime', 'date', 'time',
            'timestamp', 'binary', 'tinyBinary', 'longBinary', 'json',
        ])]
        private string $type,
        private ?string $name = null,
        private ?string $property = null,
        private bool $primary = false,
        private bool $nullable = false,
        private mixed $default = null,
        private mixed $typecast = null,
        private bool $castDefault = false,
        /**
         * @var array Database engine specific attributes
         */
        #[ArrayShape(['unsigned' => 'bool', 'zerofill' => 'bool'])]
        private array   $attributes = [],
    ) {
        if ($default !== null) {
            $this->hasDefault = true;
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

    public function getAttributes(): array
    {
        return $this->attributes;
    }
}
