<?php declare(strict_types=1);
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Cycle\Annotated\Annotation\Relation;

use Spiral\Annotations\AbstractAnnotation;
use Spiral\Annotations\Parser;

final class Inverse extends AbstractAnnotation
{
    protected const INVERSED = 'inverse';
    protected const SCHEMA   = [
        'type' => Parser::STRING,
        'name' => Parser::STRING
    ];

    /** @var string|null */
    protected $type;

    /** @var string|null */
    protected $name;

    /**
     * @return bool
     */
    public function isValid(): bool
    {
        return $this->type !== null && $this->name !== null;
    }

    /**
     * @return string|null
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * @return string|null
     */
    public function getRelation(): ?string
    {
        return $this->name;
    }
}