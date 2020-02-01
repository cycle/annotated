<?php

/**
 * Cycle ORM Schema Builder.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

declare(strict_types=1);

namespace Cycle\Schema\Definition\Map;

use Cycle\Schema\Exception\OptionException;

final class OptionMap implements \IteratorAggregate
{
    /** @var array */
    private $options = [];

    /**
     * @param string $name
     * @return bool
     */
    public function has(string $name): bool
    {
        return array_key_exists($name, $this->options);
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function get(string $name)
    {
        if (!$this->has($name)) {
            throw new OptionException("Undefined option `{$name}`");
        }

        return $this->options[$name];
    }

    /**
     * @param string $name
     * @param mixed  $value
     * @return OptionMap
     */
    public function set(string $name, $value): self
    {
        $this->options[$name] = $value;

        return $this;
    }

    /**
     * @param string $name
     * @return OptionMap
     */
    public function remove(string $name): self
    {
        unset($this->options[$name]);

        return $this;
    }

    /**
     * @return \Traversable
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->options);
    }
}
