<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */
declare(strict_types=1);

namespace Cycle\Annotated\Annotation;

use Cycle\Annotated\Annotation\Table\Index;
use Doctrine\Common\Annotations\Annotation\Target;
use Spiral\Annotations\AbstractAnnotation;

/**
 * @Annotation
 * @Target({"CLASS", "ANNOTATION"})
 */
final class Table extends AbstractAnnotation
{
    /** @var array<Column> */
    protected $columns = [];

    /** @var array<Index> */
    protected $indexes = [];

    /**
     * @return Column[]
     */
    public function getColumns(): array
    {
        return $this->columns;
    }

    /**
     * @return Index[]
     */
    public function getIndexes(): array
    {
        return $this->indexes;
    }
}