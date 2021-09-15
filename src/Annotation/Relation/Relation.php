<?php

declare(strict_types=1);

namespace Cycle\Annotated\Annotation\Relation;

use Doctrine\Common\Annotations\Annotation\Enum;
use Doctrine\Common\Annotations\Annotation\Required;

abstract class Relation implements RelationInterface
{
    // relation type
    protected const TYPE = '';

    /**
     * @Required()
     */
    protected ?string $target = null;

    /**
     * @Enum({"eager", "lazy", "promise"}
     */
    protected ?string $load = null;

    /**
     * @param array<string, mixed> $values
     */
    public function __construct(array $values)
    {
        foreach ($values as $key => $value) {
            if ($key === 'fetch') {
                $key = 'load';
            }

            $this->$key = $value;
        }
    }

    public function getType(): string
    {
        return static::TYPE;
    }

    public function getTarget(): ?string
    {
        return $this->target;
    }

    /**
     * @return array<string, mixed>
     */
    public function getOptions(): array
    {
        $options = get_object_vars($this);
        unset($options['target'], $options['inverse']);

        return $options;
    }
}
