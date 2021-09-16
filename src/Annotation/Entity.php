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
 *      @Attribute("columns", type="array<Cycle\Annotated\Annotation\Column>"),
 * })
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
final class Entity
{
    /** @var string */
    private $role;

    /** @var string */
    private $mapper;

    /** @var string */
    private $repository;

    /** @var string */
    private $table;

    /** @var bool */
    private $readonlySchema = false;

    /** @var string */
    private $database;

    /** @var string */
    private $source;

    /**
     * @var string
     *
     * @deprecated
     */
    private $constrain;

    /** @var string */
    private $scope;

    /** @var array<Column> */
    private $columns = [];

    /**
     * @param array $values
     */
    public function __construct(array $values = [])
    {
        foreach ($values as $key => $value) {
            $this->$key = $value;
        }
    }

    /**
     * @return string|null
     */
    public function getRole(): ?string
    {
        return $this->role;
    }

    /**
     * @return string|null
     */
    public function getMapper(): ?string
    {
        return $this->mapper;
    }

    /**
     * @return string|null
     */
    public function getRepository(): ?string
    {
        return $this->repository;
    }

    /**
     * @return string|null
     */
    public function getTable(): ?string
    {
        return $this->table;
    }

    /**
     * @return bool
     */
    public function isReadonlySchema(): bool
    {
        return $this->readonlySchema;
    }

    /**
     * @return string|null
     */
    public function getDatabase(): ?string
    {
        return $this->database;
    }

    /**
     * @return string|null
     */
    public function getSource(): ?string
    {
        return $this->source;
    }

    /**
     * @return string|null
     */
    public function getConstrain(): ?string
    {
        return $this->constrain ?? $this->scope;
    }

    /**
     * @return Column[]
     */
    public function getColumns(): array
    {
        return $this->columns;
    }
}
