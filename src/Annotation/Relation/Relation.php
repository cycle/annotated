<?php

declare(strict_types=1);

namespace Cycle\Annotated\Annotation\Relation;

abstract class Relation implements RelationInterface
{
    // relation type
    protected const TYPE = '';

    /**
     * @param non-empty-string|null $target
     * @param non-empty-string $load
     */
    public function __construct(
        protected ?string $target,
        protected string $load = 'lazy',
    ) {}

    /**
     * @return non-empty-string
     */
    public function getType(): string
    {
        return static::TYPE;
    }

    /**
     * @return non-empty-string|null
     */
    public function getTarget(): ?string
    {
        return $this->target;
    }

    /**
     * @return non-empty-string
     */
    public function getLoad(): string
    {
        return $this->load;
    }

    public function getOptions(): array
    {
        $options = \get_object_vars($this);
        unset($options['target'], $options['inverse']);

        return $options;
    }
}
