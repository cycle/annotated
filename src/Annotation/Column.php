<?php

declare(strict_types=1);

namespace Cycle\Annotated\Annotation;

use Doctrine\Common\Annotations\Annotation\NamedArgumentConstructor;
use Doctrine\Common\Annotations\Annotation\Target;
use JetBrains\PhpStorm\ExpectedValues;

/**
 * @Annotation
 * @NamedArgumentConstructor
 * @Target({"PROPERTY", "ANNOTATION", "CLASS"})
 */
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_CLASS | \Attribute::IS_REPEATABLE), NamedArgumentConstructor]
final class Column
{
    private bool $hasDefault = false;

    public function __construct(
        /**
         * @var non-empty-string|null
         *
         * @see \Cycle\Database\Schema\AbstractColumn::$mapping
         */
        #[ExpectedValues(values: ['primary', 'bigPrimary', 'enum', 'boolean', 'integer', 'tinyInteger', 'bigInteger',
            'string', 'text', 'tinyText', 'longText', 'double', 'float', 'decimal', 'datetime', 'date', 'time',
            'timestamp', 'binary', 'tinyBinary', 'longBinary', 'json',
        ])]
        private string $type,
        /**  @var non-empty-string|null */
        private ?string $name = null,
        /**  @var non-empty-string|null */
        private ?string $property = null,
        private bool $primary = false,
        private bool $nullable = false,
        private mixed $default = null,
        private mixed $typecast = null,
        private bool $castDefault = false,
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
}
