<?php declare(strict_types=1);
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Cycle\Annotated\Annotation\Relation;

use Spiral\Annotations\AnnotationInterface;
use Spiral\Annotations\Parser;

abstract class Relation implements RelationInterface, AnnotationInterface
{
    protected const NAME    = '';
    protected const OPTIONS = [
        'cascade'         => Parser::BOOL,
        'nullable'        => Parser::BOOL,
        'innerKey'        => Parser::STRING,
        'outerKey'        => Parser::STRING,
        'morphKey'        => Parser::STRING,
        'morphKeyLength'  => Parser::INTEGER,
        'though'          => Parser::STRING,
        'thoughInnerKey'  => Parser::STRING,
        'thoughOuterKey'  => Parser::STRING,
        'thoughConstrain' => Parser::STRING,
        'thoughWhere'     => [Parser::MIXED],
        'where'           => [Parser::MIXED],
        'fkCreate'        => Parser::BOOL,
        'fkAction'        => Parser::STRING,
        'indexCreate'     => Parser::BOOL,
    ];

    /** @var string|null */
    protected $target;

    /** @var Inverse|null */
    protected $inversed;

    /** @var array */
    protected $options = [];

    /**
     * Public and unique node name.
     *
     * @return string
     */
    public function getName(): string
    {
        return static::NAME;
    }

    /**
     * Return Node schema in a form of [name => Node|SCALAR|[Node]].
     *
     * @return array
     */
    public function getSchema(): array
    {
        return static::OPTIONS + [
                'target'  => Parser::STRING,
                'inverse' => Inverse::class
            ];
    }

    /**
     * Set node attribute value.
     *
     * @param string $name
     * @param mixed  $value
     */
    public function setAttribute(string $name, $value)
    {
        if (in_array($name, ['target', 'inversed'])) {
            $this->{$name} = $value;
            return;
        }

        $this->options[$name] = $value;
    }

    /**
     * @return string|null
     */
    public function getTarget(): ?string
    {
        return $this->target;
    }

    /**
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * @return bool
     */
    public function isInversed(): bool
    {
        return $this->inversed !== null && $this->inversed->isValid();
    }

    /**
     * @return string
     */
    public function getInverseType(): string
    {
        return $this->inversed->getType();

    }

    /**
     * @return string
     */
    public function getInverseName(): string
    {
        return $this->inversed->getRelation();
    }
}