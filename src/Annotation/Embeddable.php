<?php declare(strict_types=1);
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Cycle\Annotated\Annotation;

use Spiral\Annotations\AbstractAnnotation;
use Spiral\Annotations\Parser;

final class Embeddable extends AbstractAnnotation
{
    public const NAME   = 'embeddable';
    public const SCHEMA = [
        'role'         => Parser::STRING,
        'mapper'       => Parser::STRING,
        'columnPrefix' => Parser::STRING,
        'columns'      => [Column::class],
    ];

    /** @var string|null */
    protected $role;

    /** @var string|null */
    protected $mapper;

    /** @var string */
    protected $columnPrefix = '';

    /** @var array */
    protected $columns = [];

    /**
     * @return string|null
     */
    public function getRole(): ?string
    {
        return $this->role;
    }

    /**
     * @return string|null
     */
    public function getMapper(): ?string
    {
        return $this->mapper;
    }

    /**
     * @return string
     */
    public function getColumnPrefix(): string
    {
        return $this->columnPrefix;
    }

    /**
     * @return Column[]
     */
    public function getColumns(): array
    {
        return $this->columns;
    }
}