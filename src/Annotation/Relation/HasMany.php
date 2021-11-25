<?php

declare(strict_types=1);

namespace Cycle\Annotated\Annotation\Relation;

use Cycle\Annotated\Annotation\Relation\Traits\InverseTrait;
use Doctrine\Common\Annotations\Annotation\NamedArgumentConstructor;
use JetBrains\PhpStorm\ExpectedValues;

/**
 * @Annotation
 * @NamedArgumentConstructor
 * @Target("PROPERTY")
 */
#[\Attribute(\Attribute::TARGET_PROPERTY), NamedArgumentConstructor]
final class HasMany extends Relation
{
    use InverseTrait;

    protected const TYPE = 'hasMany';

    public function __construct(
        string $target,
        /**
         * Inner key in parent entity. Defaults to the primary key
         */
        protected array|string|null $innerKey = null,
        /**
         * Outer key name. Defaults to {parentRole}_{innerKey}
         */
        protected array|string|null $outerKey = null,
        /**
         * Automatically save related data with parent entity. Defaults to true
         */
        protected bool $cascade = true,
        /**
         * Defines if the relation can be nullable (child can have no parent). Defaults to false
         */
        protected bool $nullable = false,
        /**
         * Additional where condition to be applied for the relation. Defaults to none
         */
        protected array $where = [],
        /**
         * Additional sorting rules. Defaults to none
         */
        protected array $orderBy = [],
        /**
         * Set to true to automatically create FK on outerKey. Defaults to true
         */
        protected bool $fkCreate = true,
        /**
         * FK onDelete and onUpdate action. Defaults to CASCADE
         * @Enum({"NO ACTION", "CASCADE", "SET NULL"})
         */
        #[ExpectedValues(values: ['NO ACTION', 'CASCADE', 'SET NULL'])]
        protected ?string $fkAction = 'CASCADE',
        /**
         * FK onDelete action. It has higher priority than {$fkAction}. Defaults to @see {$fkAction}
         * @Enum({"NO ACTION", "CASCADE", "SET NULL"})
         */
        #[ExpectedValues(values: ['NO ACTION', 'CASCADE', 'SET NULL'])]
        protected ?string $fkOnDelete = null,
        /**
         * Create an index on outerKey. Defaults to true
         */
        protected bool $indexCreate = true,
        /**
         * Collection that will contain loaded entities. Defaults to `array`
         */
        protected ?string $collection = null,
        /**
         * Relation load approach. Defaults to lazy
         */
        #[ExpectedValues(values: ['lazy', 'eager'])]
        string $load = 'lazy',
        ?Inverse $inverse = null,
    ) {
        $this->inverse = $inverse;

        parent::__construct($target, $load);
    }
}
