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
use Doctrine\Common\Annotations\Annotation\Target;
use Spiral\Annotations\AbstractAnnotation;

/**
 * @Annotation
 * @Target("ANNOTATION")
 */
class Index extends AbstractAnnotation
{
    /** @var string */
    protected $name;

    /** @var bool */
    protected $unique = false;

    /** @var array<Column> */
    protected $columns = [];

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

    /**
     * @return Column[]
     */
    public function getColumns(): array
    {
        return $this->columns;
    }
}