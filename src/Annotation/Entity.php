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
final class Entity
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
     */
    public function __construct(
        private ?string $role = null,
        private ?string $mapper = null,
        private ?string $repository = null,
        private ?string $table = null,
        private bool $readonlySchema = false,
        private ?string $database = null,
        private ?string $source = null,
        private array|string|null $typecast = null,
        private ?string $scope = null,
        private array $columns = [],
        /** @deprecated Use {@see $scope} instead */
        private ?string $constrain = null,
    ) {
    }

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function getMapper(): ?string
    {
        return $this->mapper;
    }

    public function getRepository(): ?string
    {
        return $this->repository;
    }

    public function getTable(): ?string
    {
        return $this->table;
    }

    public function isReadonlySchema(): bool
    {
        return $this->readonlySchema;
    }

    public function getDatabase(): ?string
    {
        return $this->database;
    }

    public function getSource(): ?string
    {
        return $this->source;
    }

    public function getScope(): ?string
    {
        return $this->scope ?? $this->constrain;
    }

    public function getColumns(): array
    {
        return $this->columns;
    }

    public function getTypecast(): array|string|null
    {
        if (is_array($this->typecast) && count($this->typecast) === 1) {
            return reset($this->typecast);
        }

        return $this->typecast;
    }
}
