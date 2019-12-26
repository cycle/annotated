<?php

/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

declare(strict_types=1);

namespace Cycle\Annotated\Annotation\Table;

use Cycle\Annotated\Annotation\Column;
use Doctrine\Common\Annotations\Annotation\Attribute;
use Doctrine\Common\Annotations\Annotation\Attributes;
use Doctrine\Common\Annotations\Annotation\Target;

/**
 * @Annotation
 * @Target("ANNOTATION")
 * @Attributes({
 *      @Attribute("columns", type="array<string>", required=true),
 *      @Attribute("unique", type="bool"),
 *      @Attribute("name", type="string"),
 * })
 */
class Index
{
    /** @var array<string> */
    private $columns = [];

    /** @var bool */
    private $unique = false;

    /** @var string */
    private $name;

    /**
     * @param array $values
     */
    public function __construct(array $values)
    {
        foreach ($values as $key => $value) {
            $this->$key = $value;
        }
    }

    /**
     * @return Column[]
     */
    public function getColumns(): array
    {
        return $this->columns;
    }


    /**
     * @return string|null
     */
    public function getIndex(): ?string
    {
        return $this->name;
    }

    /**
     * @return bool
     */
    public function isUnique(): bool
    {
        return $this->unique;
    }
}
