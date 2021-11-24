<?php

declare(strict_types=1);

namespace Cycle\Annotated\Annotation;

use Doctrine\Common\Annotations\Annotation\NamedArgumentConstructor;
use Doctrine\Common\Annotations\Annotation\Target;

/**
 * @Annotation
 * @NamedArgumentConstructor
 * @Target("CLASS")
 */
#[\Attribute(\Attribute::TARGET_CLASS), NamedArgumentConstructor]
final class Entity
{
    public function __construct(
        /**  @var non-empty-string|null */
        private ?string $role = null,
        /** @var class-string|null */
        private ?string $mapper = null,
        /** @var class-string|null */
        private ?string $repository = null,
        /**  @var non-empty-string|null */
        private ?string $table = null,
        private bool $readonlySchema = false,
        /**  @var non-empty-string|null */
        private ?string $database = null,
        private ?string $source = null,
        /**  @var non-empty-string|non-empty-string[]|null */
        private array|string|null $typecast = null,
        /** @deprecated Use {@see $scope} instead */
        private ?string $constrain = null,
        private ?string $scope = null,
        /** @var Column[] */
        private array $columns = [],
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
