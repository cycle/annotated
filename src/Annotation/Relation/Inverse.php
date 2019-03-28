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
    protected const NAME   = 'inverse';
    protected const SCHEMA = [
        'type' => Parser::STRING,
        'name' => Parser::STRING,
        'as'   => Parser::STRING, // alias to name
    ];

    /** @var string|null */
    protected $type;

    /** @var string|null */
    protected $name;

    /**
     * @inheritdoc
     */
    public function setAttribute(string $name, $value)
    {
        if ($name == "as") {
            $name = "name";
        }

        parent::setAttribute($name, $value);
    }

    /**
     * @return bool
     */
    public function isValid(): bool
    {
        return $this->getType() !== null && $this->getRelation() !== null;
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
        return $this->name ?? $this->as;
    }
}