<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */
declare(strict_types=1);

namespace Cycle\Annotated\Annotation;

use Doctrine\Common\Annotations\Annotation\Target;

/**
 * @Annotation
 * @Target("CLASS")
 */
final class Entity
{
    /** @var string */
    public $role;

    /** @var string */
    protected $mapper;

    /** @var string */
    protected $repository;

    /** @var string */
    protected $table;

    /** @var bool */
    protected $readonlySchema = false;

    /** @var string */
    protected $database;

    /** @var string */
    protected $source;

    /** @var string */
    protected $constrain;

    /** @var array<Column> */
    protected $columns = [];

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
        return $this->constrain;
    }

    /**
     * @return Column[]
     */
    public function getColumns(): array
    {
        return $this->columns;
    }
}