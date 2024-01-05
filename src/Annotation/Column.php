<?php

declare(strict_types=1);

namespace Cycle\Annotated\Annotation;

use Cycle\ORM\Parser\Typecast;
use Doctrine\Common\Annotations\Annotation\Target;
use JetBrains\PhpStorm\ExpectedValues;
use Spiral\Attributes\NamedArgumentConstructor;

/**
 * @Annotation
 * @NamedArgumentConstructor
 * @Target({"PROPERTY", "ANNOTATION", "CLASS"})
 */
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_CLASS | \Attribute::IS_REPEATABLE)]
#[NamedArgumentConstructor]
class Column
{
    protected bool $hasDefault = false;
    /**
     * @var array<non-empty-string, mixed> Other database specific attributes.
     */
    protected array $attributes;

    /**
     * @param non-empty-string $type Column type. {@see \Cycle\Database\Schema\AbstractColumn::$mapping}
     *        Column types `smallPrimary`, `timetz`, `timestamptz`, `interval`, `bitVarying`, `int4range`, `int8range`,
     *        `numrange`, `tsrange`, `tstzrange`, `daterange`, `jsonb`, `point`, `line`, `lseg`, `box`, `path`,
     *        `polygon`, `circle`, `cidr`, `inet`, `macaddr`, `macaddr8`, `tsvector`, `tsquery` are related
     *         to the PostgreSQL only {@see \Cycle\Database\Driver\Postgres\Schema\PostgresColumn::$mapping}
     *        Column type `datetime2` is related to the SQL Server only
     *        {@see \Cycle\Database\Driver\SQLServer\Schema\SQLServerColumn::$mapping}
     * @param non-empty-string|null $name Column name. Defaults to the property name.
     * @param non-empty-string|null $property Property that belongs to column. For virtual columns.
     * @param bool $primary Explicitly set column as a primary key.
     * @param bool $nullable Set column as nullable.
     * @param mixed|null $default Default column value.
     * @param callable|non-empty-string|null $typecast Typecast rule name.
     *        Regarding the default Typecast handler {@see Typecast} the value can be `callable` or
     *        one of ("int"|"float"|"bool"|"datetime") based on column type.
     *        If you want to use another rule you should add in the `typecast` argument of the {@see Entity} attribute
     *        a relevant Typecast handler that supports the rule.
     * @param bool $readonlySchema Set to true to disable schema synchronization for the assigned column.
     * @param mixed ...$attributes Other database specific attributes. Use named notation to define them.
     *        For example: #[Column('smallInt', unsigned: true, zerofill: true)]
     */
    public function __construct(
        #[ExpectedValues(values: ['primary', 'bigPrimary', 'enum', 'boolean',
            'integer', 'tinyInteger', 'smallInteger', 'bigInteger', 'string', 'text', 'tinyText', 'longText', 'double',
            'float', 'decimal', 'datetime', 'date', 'time', 'timestamp', 'binary', 'tinyBinary', 'longBinary', 'json',
            'uuid', 'bit',
            // PostgreSQL
            'smallPrimary', 'timetz', 'timestamptz', 'interval', 'bitVarying', 'int4range', 'int8range', 'numrange',
            'tsrange', 'tstzrange', 'daterange', 'jsonb', 'point', 'line', 'lseg', 'box', 'path', 'polygon', 'circle',
            'cidr', 'inet', 'macaddr', 'macaddr8', 'tsvector', 'tsquery',
            // SQL Server
            'datetime2',
        ])]
        protected string $type,
        protected ?string $name = null,
        protected ?string $property = null,
        protected bool $primary = false,
        protected bool $nullable = false,
        protected mixed $default = null,
        protected mixed $typecast = null,
        protected bool $castDefault = false,
        protected bool $readonlySchema = false,
        mixed ...$attributes,
    ) {
        if ($default !== null) {
            $this->hasDefault = true;
        }
        $this->attributes = $attributes;
    }

    /**
     * @return non-empty-string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return non-empty-string|null
     */
    public function getColumn(): ?string
    {
        return $this->name;
    }

    /**
     * @return non-empty-string|null
     */
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

    public function isReadonlySchema(): bool
    {
        return $this->readonlySchema;
    }

    /**
     * @return array<non-empty-string, mixed>
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }
}
