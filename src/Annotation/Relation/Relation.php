<?php

/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

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
     * @var string
     */
    protected $target;

    /**
     * @Enum({"eager", "lazy", "promise"}
     * @var string
     */
    protected $load;

    /**
     * @param array $values
     */
    public function __construct(array $values)
    {
        foreach ($values as $key => $value) {
            if ($key == 'fetch') {
                $key = 'load';
            }

            $this->$key = $value;
        }
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return static::TYPE;
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
        $options = get_object_vars($this);
        unset($options['target'], $options['inverse']);

        return $options;
    }
}
