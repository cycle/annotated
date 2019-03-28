<?php declare(strict_types=1);
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Cycle\Annotated\Annotation\Table;

use Spiral\Annotations\AbstractAnnotation;
use Spiral\Annotations\Parser;

class Index extends AbstractAnnotation
{
    public const NAME   = 'index';
    public const SCHEMA = [
        'name'    => Parser::STRING,
        'unique'  => Parser::BOOL,
        'columns' => [Parser::STRING],
    ];

    /** @var string|null */
    protected $name;

    /** @var bool */
    protected $unique = false;

    /** @var array */
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
     * @return array
     */
    public function getColumns(): array
    {
        return $this->columns;
    }
}