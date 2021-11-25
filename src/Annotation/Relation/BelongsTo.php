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
final class BelongsTo extends Relation
{
    use InverseTrait;

    protected const TYPE = 'belongsTo';

    public function __construct(
        string $target,
        /**
         * Inner key in source entity. Defaults to {relationName}_{outerKey}
         */
        protected array|string|null $innerKey = null,
        /**
         * Outer key in the related entity. Defaults to the primary key
         */
        protected array|string|null $outerKey = null,
        /**
         * Automatically save related data with source entity. Defaults to true
         */
        protected bool $cascade = true,
        /**
         * Defines if the relation can be nullable (child can have no parent). Defaults to false
         */
        protected bool $nullable = false,
        /**
         * Set to true to automatically create FK on innerKey. Defaults to true
         */
        protected bool $fkCreate = true,
        /**
         * FK onDelete and onUpdate action. Defaults to CASCADE
         * @Enum({"NO ACTION", "CASCADE", "SET NULL"})
         */
        #[ExpectedValues(values: ['NO ACTION', 'CASCADE', 'SET NULL'])]
        protected string $fkAction = 'CASCADE',
        /**
         * FK onDelete action. It has higher priority than {$fkAction}. Defaults to @see {$fkAction}
         * @Enum({"NO ACTION", "CASCADE", "SET NULL"})
         */
        #[ExpectedValues(values: ['NO ACTION', 'CASCADE', 'SET NULL'])]
        protected ?string $fkOnDelete = null,
        /**
         * Create an index on innerKey. Defaults to true
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
