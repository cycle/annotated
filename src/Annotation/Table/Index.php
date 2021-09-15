<?php

declare(strict_types=1);

namespace Cycle\Annotated\Annotation\Table;

use Doctrine\Common\Annotations\Annotation\Attribute;
use Doctrine\Common\Annotations\Annotation\Attributes;
use Doctrine\Common\Annotations\Annotation\Target;

/**
 * @Annotation
 * @Target("ANNOTATION", "CLASS")
 * @Attributes({
 *      @Attribute("columns", type="array<string>", required=true),
 *      @Attribute("unique", type="bool"),
 *      @Attribute("name", type="string"),
 * })
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::IS_REPEATABLE)]
class Index
{
    /** @var string[] */
    private array $columns = [];

    private bool $unique = false;

    private ?string $name = null;

    /**
     * @param array<string, mixed> $values
     */
    public function __construct(array $values)
    {
        foreach ($values as $key => $value) {
            $this->$key = $value;
        }
    }

    /**
     * @return string[]
     */
    public function getColumns(): array
    {
        return $this->columns;
    }

    public function getIndex(): ?string
    {
        return $this->name;
    }

    public function isUnique(): bool
    {
        return $this->unique;
    }
}
