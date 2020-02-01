<?php

/**
 * Cycle ORM Schema Builder.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

declare(strict_types=1);

namespace Cycle\Schema\Definition\Map;

use Cycle\Schema\Definition\Field;
use Cycle\Schema\Exception\FieldException;

/**
 * Manage the set of fields associated with the entity.
 */
final class FieldMap implements \IteratorAggregate, \Countable
{
    /** @var Field[] */
    private $fields = [];

    /**
     * Cloning.
     */
    public function __clone()
    {
        foreach ($this->fields as $name => $field) {
            $this->fields[$name] = clone $field;
        }
    }

    /**
     * @return int
     */
    public function count(): int
    {
        return count($this->fields);
    }

    /**
     * @param string $name
     * @return bool
     */
    public function has(string $name): bool
    {
        return isset($this->fields[$name]);
    }

    /**
     * @param string $name
     * @return Field
     */
    public function get(string $name): Field
    {
        if (!$this->has($name)) {
            throw new FieldException("Undefined field `{$name}`");
        }

        return $this->fields[$name];
    }

    /**
     * @param string $name
     * @param Field  $field
     * @return FieldMap
     */
    public function set(string $name, Field $field): self
    {
        if ($this->has($name)) {
            throw new FieldException("Field `{$name}` already exists");
        }

        $this->fields[$name] = $field;

        return $this;
    }

    /**
     * @param string $name
     * @return FieldMap
     */
    public function remove(string $name): self
    {
        unset($this->fields[$name]);
        return $this;
    }

    /**
     * @return Field[]|\Traversable
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->fields);
    }
}
