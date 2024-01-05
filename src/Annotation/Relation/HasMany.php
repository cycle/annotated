<?php

declare(strict_types=1);

namespace Cycle\Annotated\Annotation\Relation;

use Cycle\Annotated\Annotation\Relation\Traits\InverseTrait;
use JetBrains\PhpStorm\ExpectedValues;
use Spiral\Attributes\NamedArgumentConstructor;

/**
 * @Annotation
 * @NamedArgumentConstructor
 * @Target("PROPERTY")
 */
#[\Attribute(\Attribute::TARGET_PROPERTY), NamedArgumentConstructor]
class HasMany extends Relation
{
    use InverseTrait;

    protected const TYPE = 'hasMany';

    /**
     * @param non-empty-string $target
     * @param array|non-empty-string|null $innerKey Inner key in parent entity. Defaults to the primary key.
     * @param array|non-empty-string|null $outerKey Outer key name. Defaults to {parentRole}_{innerKey}.
     * @param bool $cascade Automatically save related data with parent entity.
     * @param bool $nullable Defines if the relation can be nullable (child can have no parent).
     * @param array $where Additional where condition to be applied for the relation.
     * @param array $orderBy Additional sorting rules.
     * @param bool $fkCreate Set to true to automatically create FK on outerKey.
     * @param non-empty-string $fkAction FK onDelete and onUpdate action.
     * @param non-empty-string|null $fkOnDelete FK onDelete action. It has higher priority than {@see $fkAction}.
     *        Defaults to {@see $fkAction}.
     * @param bool $indexCreate Create an index on outerKey.
     * @param non-empty-string|null $collection Collection that will contain loaded entities.
     * @param non-empty-string $load Relation load approach.
     */
    public function __construct(
        string $target,
        protected array|string|null $innerKey = null,
        protected array|string|null $outerKey = null,
        protected bool $cascade = true,
        protected bool $nullable = false,
        protected array $where = [],
        protected array $orderBy = [],
        protected bool $fkCreate = true,
        /**
         * @Enum({"NO ACTION", "CASCADE", "SET NULL"})
         */
        #[ExpectedValues(values: ['NO ACTION', 'CASCADE', 'SET NULL'])]
        protected string $fkAction = 'CASCADE',
        /**
         * @Enum({"NO ACTION", "CASCADE", "SET NULL"})
         */
        #[ExpectedValues(values: ['NO ACTION', 'CASCADE', 'SET NULL'])]
        protected ?string $fkOnDelete = null,
        protected bool $indexCreate = true,
        protected ?string $collection = null,
        #[ExpectedValues(values: ['lazy', 'eager'])]
        string $load = 'lazy',
        ?Inverse $inverse = null,
    ) {
        $this->inverse = $inverse;

        parent::__construct($target, $load);
    }
}
