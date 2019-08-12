<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */
declare(strict_types=1);

namespace Cycle\Annotated\Annotation;

use Doctrine\Common\Annotations\Annotation\Target;
use Spiral\Annotations\AbstractAnnotation;

/**
 * @Annotation
 * @Target("CLASS")
 */
final class Embeddable extends AbstractAnnotation
{
    /** @var string */
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