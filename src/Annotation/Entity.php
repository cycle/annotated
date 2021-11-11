<?php

declare(strict_types=1);

namespace Cycle\Annotated\Annotation;

use Doctrine\Common\Annotations\Annotation\Attribute;
use Doctrine\Common\Annotations\Annotation\Attributes;
use Doctrine\Common\Annotations\Annotation\Target;

/**
 * @Annotation
 * @Target("CLASS")
 * @Attributes({
 *      @Attribute("role", type="string"),
 *      @Attribute("mapper", type="string"),
 *      @Attribute("repository", type="string"),
 *      @Attribute("table", type="string"),
 *      @Attribute("database", type="string"),
 *      @Attribute("source", type="string"),
 *      @Attribute("constrain", type="string"),
 *      @Attribute("scope", type="string"),
 *      @Attribute("typecast", type="array<string>"),
 *      @Attribute("columns", type="array<Cycle\Annotated\Annotation\Column>"),
 * })
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
final class Entity
{
    private ?string $role = null;

    private ?string $mapper = null;

    private ?string $repository = null;

    private ?string $table = null;

    private bool $readonlySchema = false;

    private ?string $database = null;

    private ?string $source = null;

    private array|string|null $typecast = null;

    /** @deprecated Use {@see $scope} instead */
    private ?string $constrain = null;

    private ?string $scope = null;

    /** @var Column[] */
    private array $columns = [];

    /**
     * @param array<string, mixed> $values
     */
    public function __construct(array $values = [])
    {
        foreach ($values as $key => $value) {
            $this->$key = $value;
        }
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

    /**
     * @return Column[]
     */
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
