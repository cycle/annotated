<?php

/**
 * Cycle ORM Schema Builder.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

declare(strict_types=1);

namespace Cycle\Schema\Definition\Map;

use Cycle\Schema\Definition\Relation;
use Cycle\Schema\Exception\RelationException;

final class RelationMap implements \IteratorAggregate
{
    /** @var Relation[] */
    private $relations = [];

    /**
     * Cloning.
     */
    public function __clone()
    {
        foreach ($this->relations as $name => $relation) {
            $this->relations[$name] = clone $relation;
        }
    }

    /**
     * @param string $name
     * @return bool
     */
    public function has(string $name): bool
    {
        return isset($this->relations[$name]);
    }

    /**
     * @param string $name
     * @return Relation
     */
    public function get(string $name): Relation
    {
        if (!$this->has($name)) {
            throw new RelationException("Undefined relation `{$name}`");
        }

        return $this->relations[$name];
    }

    /**
     * @param string   $name
     * @param Relation $relation
     * @return RelationMap
     */
    public function set(string $name, Relation $relation): self
    {
        if ($this->has($name)) {
            throw new RelationException("Relation `{$name}` already exists");
        }

        $this->relations[$name] = $relation;

        return $this;
    }

    /**
     * @param string $name
     * @return RelationMap
     */
    public function remove(string $name): self
    {
        unset($this->relations[$name]);
        return $this;
    }

    /**
     * @return Relation[]|\Traversable
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->relations);
    }
}
