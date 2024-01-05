<?php

declare(strict_types=1);

namespace Cycle\Annotated\Annotation;

use Doctrine\Common\Annotations\Annotation\Target;
use Spiral\Attributes\NamedArgumentConstructor;

/**
 * @Annotation
 * @NamedArgumentConstructor
 * @Target("CLASS")
 */
#[\Attribute(\Attribute::TARGET_CLASS), NamedArgumentConstructor]
class Entity
{
    /**
     * @param non-empty-string|null $role Entity role. Defaults to the lowercase class name without a namespace.
     * @param class-string|null $mapper Mapper class name. Defaults to {@see \Cycle\ORM\Mapper\Mapper}
     * @param class-string|null $repository Repository class to represent read operations for an entity.
     *        Defaults to {@see \Cycle\ORM\Select\Repository}
     * @param non-empty-string|null $table Entity source table. Defaults to plural form of entity role.
     * @param bool $readonlySchema Set to true to disable schema synchronization for the assigned table.
     * @param non-empty-string|null $database Database name. Defaults to null (default database).
     * @param class-string|null $source Entity source class (internal). Defaults to {@see \Cycle\ORM\Select\Source}
     * @param non-empty-string|non-empty-string[]|null $typecast
     * @param class-string|null $scope Class name of constraint to be applied to every entity query.
     * @param Column[] $columns Entity columns.
     * @param ForeignKey[] $foreignKeys Entity foreign keys.
     */
    public function __construct(
        protected ?string $role = null,
        protected ?string $mapper = null,
        protected ?string $repository = null,
        protected ?string $table = null,
        protected bool $readonlySchema = false,
        protected ?string $database = null,
        protected ?string $source = null,
        protected array|string|null $typecast = null,
        protected ?string $scope = null,
        protected array $columns = [],
        protected array $foreignKeys = [],
    ) {
    }

    /**
     * @return non-empty-string|null
     */
    public function getRole(): ?string
    {
        return $this->role;
    }

    /**
     * @return class-string|null
     */
    public function getMapper(): ?string
    {
        return $this->mapper;
    }

    /**
     * @return class-string|null
     */
    public function getRepository(): ?string
    {
        return $this->repository;
    }

    /**
     * @return non-empty-string|null
     */
    public function getTable(): ?string
    {
        return $this->table;
    }

    public function isReadonlySchema(): bool
    {
        return $this->readonlySchema;
    }

    /**
     * @return non-empty-string|null
     */
    public function getDatabase(): ?string
    {
        return $this->database;
    }

    /**
     * @return class-string|null
     */
    public function getSource(): ?string
    {
        return $this->source;
    }

    /**
     * @return class-string|null
     */
    public function getScope(): ?string
    {
        return $this->scope;
    }

    /**
     * @return Column[]
     */
    public function getColumns(): array
    {
        return $this->columns;
    }

    /**
     * @return ForeignKey[]
     */
    public function getForeignKeys(): array
    {
        return $this->foreignKeys;
    }

    /**
     * @return non-empty-string|non-empty-string[]|null
     */
    public function getTypecast(): array|string|null
    {
        if (\is_array($this->typecast) && \count($this->typecast) === 1) {
            return \reset($this->typecast);
        }

        return $this->typecast;
    }
}
