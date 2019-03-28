<?php declare(strict_types=1);
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Cycle\Annotated\Annotation;

use Cycle\Annotated\Annotation\Table\Index;
use Spiral\Annotations\AbstractAnnotation;
use Spiral\Annotations\Parser;

final class Table extends AbstractAnnotation
{
    public const NAME   = 'table';
    public const SCHEMA = [
        'readonly' => Parser::BOOL,
        'columns'  => [Column::class],
        'indexes'  => [Index::class],
    ];

    /** @var bool */
    protected $readonly = false;

    /** @var array */
    protected $columns = [];

    /** @var array */
    protected $indexes = [];

    /**
     * @return bool
     */
    public function isReadonly(): bool
    {
        return $this->readonly;
    }

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