<?php

declare(strict_types=1);

namespace Cycle\Annotated\Annotation\Relation;

use Cycle\Annotated\Annotation\Relation\Traits\InverseTrait;
use Doctrine\Common\Annotations\Annotation\Enum;
use Doctrine\Common\Annotations\Annotation\NamedArgumentConstructor;
use JetBrains\PhpStorm\ExpectedValues;

/**
 * @Annotation
 * @NamedArgumentConstructor
 * @Target("PROPERTY")
 */
#[\Attribute(\Attribute::TARGET_PROPERTY), NamedArgumentConstructor]
final class HasOne extends Relation
{
    use InverseTrait;

    protected const TYPE = 'hasOne';

    public function __construct(
        string $target,
        /**
         * Inner key in parent entity. Defaults to primary key
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
         * Defines if relation can be nullable (child can have no parent). Defaults to false
         */
        protected bool $nullable = false,
        /**
         * Set to true to automatically create FK on outerKey. Defaults to true
         */
        protected bool $fkCreate = true,
        /**
         * FK onDelete and onUpdate action. Defaults to CASCADE
         *
         * @Enum({"NO ACTION", "CASCADE", "SET NULL"})
         */
        #[ExpectedValues(values: ['NO ACTION', 'CASCADE', 'SET NULL'])]
        protected ?string $fkAction = 'CASCADE',
        /**
         * FK onDelete action. It has higher priority than {$fkAction}. Defaults to @see {$fkAction}
         *
         * @Enum({"NO ACTION", "CASCADE", "SET NULL"})
         */
        #[ExpectedValues(values: ['NO ACTION', 'CASCADE', 'SET NULL'])]
        protected ?string $fkOnDelete = null,
        /**
         * Create index on outerKey. Defaults to true
         */
        protected bool $indexCreate = true,
        /**
         * Relation load approach. Defaults to lazy
         */
        #[ExpectedValues(values: ['lazy', 'eager'])]
        string $load = 'lazy',
        ?Inverse $inverse = null
    ) {
        $this->inverse = $inverse;

        parent::__construct($target, $load);
    }
}
