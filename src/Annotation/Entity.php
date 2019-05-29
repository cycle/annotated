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

final class Entity extends AbstractAnnotation
{
    public const NAME   = 'entity';
    public const SCHEMA = [
        'role'           => Parser::STRING,
        'mapper'         => Parser::STRING,
        'repository'     => Parser::STRING,
        'table'          => Parser::STRING,
        'database'       => Parser::STRING,
        'readonlySchema' => Parser::BOOL,
        'source'         => Parser::STRING,
        'constrain'      => Parser::STRING,
        'columns'        => [Column::class],
    ];

    /** @var string|null */
    protected $role;

    /** @var string|null */
    protected $mapper;

    /** @var string|null */
    protected $repository;

    /** @var string|null */
    protected $table;

    /** @var bool */
    protected $readonlySchema = false;

    /** @var string|null */
    protected $database;

    /** @var string|null */
    protected $source;

    /** @var string|null */
    protected $constrain;

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
     * @return string|null
     */
    public function getRepository(): ?string
    {
        return $this->repository;
    }

    /**
     * @return string|null
     */
    public function getTable(): ?string
    {
        return $this->table;
    }

    /**
     * @return bool
     */
    public function isReadonlySchema(): bool
    {
        return $this->readonlySchema;
    }

    /**
     * @return string|null
     */
    public function getDatabase(): ?string
    {
        return $this->database;
    }

    /**
     * @return string|null
     */
    public function getSource(): ?string
    {
        return $this->source;
    }

    /**
     * @return string|null
     */
    public function getConstrain(): ?string
    {
        return $this->constrain;
    }

    /**
     * @return Column[]
     */
    public function getColumns(): array
    {
        return $this->columns;
    }
}