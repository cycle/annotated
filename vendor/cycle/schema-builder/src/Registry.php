<?php

/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

declare(strict_types=1);

namespace Cycle\Schema;

use Cycle\Schema\Definition\Entity;
use Cycle\Schema\Exception\RegistryException;
use Cycle\Schema\Exception\RelationException;
use Spiral\Database\DatabaseProviderInterface;
use Spiral\Database\Exception\DBALException;
use Spiral\Database\Schema\AbstractTable;

final class Registry implements \IteratorAggregate
{
    /** @var DatabaseProviderInterface */
    private $dbal;

    /** @var Entity[] */
    private $entities = [];

    /** @var \SplObjectStorage */
    private $tables;

    /** @var \SplObjectStorage */
    private $children;

    /** @var \SplObjectStorage */
    private $relations;

    /**
     * @param DatabaseProviderInterface $dbal
     */
    public function __construct(DatabaseProviderInterface $dbal)
    {
        $this->dbal = $dbal;
        $this->tables = new \SplObjectStorage();
        $this->children = new \SplObjectStorage();
        $this->relations = new \SplObjectStorage();
    }

    /**
     * @param Entity $entity
     * @return Registry
     */
    public function register(Entity $entity): Registry
    {
        foreach ($this->entities as $e) {
            if ($e->getRole() == $entity->getRole()) {
                throw new RegistryException("Duplicate entity `{$e->getRole()}`");
            }
        }

        $this->entities[] = $entity;
        $this->tables[$entity] = null;
        $this->children[$entity] = [];
        $this->relations[$entity] = [];

        return $this;
    }

    /**
     * @param string $role Entity role of class.
     * @return bool
     */
    public function hasEntity(string $role): bool
    {
        foreach ($this->entities as $entity) {
            if ($entity->getRole() === $role || $entity->getClass() === $role) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get entity by it's role.
     *
     * @param string $role Entity role or class name.
     * @return Entity
     *
     * @throws RegistryException
     */
    public function getEntity(string $role): Entity
    {
        foreach ($this->entities as $entity) {
            if ($entity->getRole() == $role || $entity->getClass() === $role) {
                return $entity;
            }
        }

        throw new RegistryException("Undefined entity `{$role}`");
    }

    /**
     * @return Entity[]|\Traversable
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->entities);
    }

    /**
     * Assign child entity to parent entity.
     *
     * @param Entity $parent
     * @param Entity $child
     *
     * @throws RegistryException
     */
    public function registerChild(Entity $parent, Entity $child): void
    {
        if (!$this->hasInstance($parent)) {
            throw new RegistryException("Undefined entity `{$parent->getRole()}`");
        }

        $children = $this->children[$parent];
        $children[] = $child;
        $this->children[$parent] = $children;

        // merge parent and child schema
        $parent->merge($child);
    }

    /**
     * Get all assigned children entities.
     *
     * @param Entity $entity
     * @return Entity[]
     */
    public function getChildren(Entity $entity): array
    {
        if (!$this->hasInstance($entity)) {
            throw new RegistryException("Undefined entity `{$entity->getRole()}`");
        }

        return $this->children[$entity];
    }

    /**
     * Associate entity with table.
     *
     * @param Entity      $entity
     * @param string|null $database
     * @param string      $table
     * @return Registry
     *
     * @throws RegistryException
     * @throws DBALException
     */
    public function linkTable(Entity $entity, ?string $database, string $table): Registry
    {
        if (!$this->hasInstance($entity)) {
            throw new RegistryException("Undefined entity `{$entity->getRole()}`");
        }

        $database = $this->dbal->database($database)->getName();

        $schema = null;
        foreach ($this->tables as $other) {
            $association = $this->tables[$other];

            if ($association === null) {
                continue;
            }

            // avoid schema duplication
            if ($association['database'] === $database && $association['table'] === $table) {
                $schema = $association['schema'];
                break;
            }
        }

        if (is_null($schema)) {
            $schema = $this->dbal->database($database)->table($table)->getSchema();
        }

        $this->tables[$entity] = [
            'database' => $database,
            'table'    => $table,
            'schema'   => $schema
        ];

        return $this;
    }

    /**
     * @param Entity $entity
     * @return bool
     *
     * @throws RegistryException
     */
    public function hasTable(Entity $entity): bool
    {
        if (!$this->hasInstance($entity)) {
            throw new RegistryException("Undefined entity `{$entity->getRole()}`");
        }

        return $this->tables[$entity] !== null;
    }

    /**
     * @param Entity $entity
     * @return string
     *
     * @throws RegistryException
     */
    public function getDatabase(Entity $entity): string
    {
        if (!$this->hasTable($entity)) {
            throw new RegistryException("Entity `{$entity->getRole()}` has no assigned table");
        }

        return $this->tables[$entity]['database'];
    }

    /**
     * @param Entity $entity
     * @return string
     *
     * @throws RegistryException
     */
    public function getTable(Entity $entity): string
    {
        if (!$this->hasTable($entity)) {
            throw new RegistryException("Entity `{$entity->getRole()}` has no assigned table");
        }

        return $this->tables[$entity]['table'];
    }

    /**
     * @param Entity $entity
     * @return AbstractTable
     *
     * @throws RegistryException
     */
    public function getTableSchema(Entity $entity): AbstractTable
    {
        if (!$this->hasTable($entity)) {
            throw new RegistryException("Entity `{$entity->getRole()}` has no assigned table");
        }

        return $this->tables[$entity]['schema'];
    }

    /**
     * Create entity relation.
     *
     * @param Entity            $entity
     * @param string            $name
     * @param RelationInterface $relation
     *
     * @throws RegistryException
     * @throws RelationException
     */
    public function registerRelation(Entity $entity, string $name, RelationInterface $relation): void
    {
        if (!$this->hasInstance($entity)) {
            throw new RegistryException("Undefined entity `{$entity->getRole()}`");
        }

        $relations = $this->relations[$entity];
        $relations[$name] = $relation;
        $this->relations[$entity] = $relations;
    }

    /**
     * @param Entity $entity
     * @param string $name
     * @return bool
     *
     * @throws RegistryException
     */
    public function hasRelation(Entity $entity, string $name): bool
    {
        if (!$this->hasInstance($entity)) {
            throw new RegistryException("Undefined entity `{$entity->getRole()}`");
        }

        return isset($this->relations[$entity][$name]);
    }

    /**
     * @param Entity $entity
     * @param string $name
     * @return RelationInterface
     */
    public function getRelation(Entity $entity, string $name): RelationInterface
    {
        if (!$this->hasRelation($entity, $name)) {
            throw new RegistryException("Undefined relation `{$entity->getRole()}`.`{$name}`");
        }

        return $this->relations[$entity][$name];
    }

    /**
     * Get all relations assigned with given entity.
     *
     * @param Entity $entity
     * @return RelationInterface[]
     */
    public function getRelations(Entity $entity): array
    {
        if (!$this->hasInstance($entity)) {
            throw new RegistryException("Undefined entity `{$entity->getRole()}`");
        }

        return $this->relations[$entity];
    }

    /**
     * @param Entity $entity
     * @return bool
     */
    protected function hasInstance(Entity $entity): bool
    {
        return array_search($entity, $this->entities, true) !== false;
    }
}
