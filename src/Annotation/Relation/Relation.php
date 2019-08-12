<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */
declare(strict_types=1);

namespace Cycle\Annotated\Annotation\Relation;

abstract class Relation
{
    /** @var string|null */
    private $target;

    /** @var Inverse|null */
    private $inverse;

    /** @var array */
    private $options = [];

    /**
     * @param array $values
     */
    public function __construct(array $values)
    {
        foreach ($values as $key => $value) {
            $this->setValue($key, $value);
        }
    }

    /**
     * Set node attribute value.
     *
     * @param string $name
     * @param mixed  $value
     */
    protected function setValue(string $name, $value)
    {
        if (in_array($name, ['target', 'inverse'])) {
            $this->{$name} = $value;
            return;
        }

        if (in_array($name, ['load', 'fetch'])) {
            $name = 'load';
        }

        $this->options[$name] = $value;
    }

    /**
     * @return string|null
     */
    public function getTarget(): ?string
    {
        return $this->target;
    }

    /**
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * @return string|null
     */
    public function getLoadMethod(): ?string
    {
        return $this->options['load'] ?? null;
    }

    /**
     * @return bool
     */
    public function isInversed(): bool
    {
        return $this->inverse !== null && $this->inverse->isValid();
    }

    /**
     * @return string
     */
    public function getInverseType(): string
    {
        return $this->inverse->getType();
    }

    /**
     * @return string
     */
    public function getInverseName(): string
    {
        return $this->inverse->getRelation();
    }

    /**
     * @return int|null
     */
    public function getInverseLoadMethod(): ?int
    {
        return $this->inverse->getLoadMethod();
    }

}