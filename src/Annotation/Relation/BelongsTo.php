<?php

declare(strict_types=1);

namespace Cycle\Annotated\Annotation\Relation;

use Cycle\Annotated\Annotation\Relation\Traits\InverseTrait;
use Doctrine\Common\Annotations\Annotation\Enum;
use JetBrains\PhpStorm\ExpectedValues;
use Spiral\Attributes\NamedArgumentConstructor;

/**
 * @Annotation
 * @NamedArgumentConstructor
 * @Target("PROPERTY")
 */
#[\Attribute(\Attribute::TARGET_PROPERTY), NamedArgumentConstructor]
class BelongsTo extends Relation
{
    use InverseTrait;

    protected const TYPE = 'belongsTo';

    /**
     * @param non-empty-string $target
     * @param array|non-empty-string|null $innerKey Inner key in source entity. Defaults to {relationName}_{outerKey}.
     * @param array|non-empty-string|null $outerKey Outer key in the related entity. Defaults to the primary key.
     * @param bool $cascade Automatically save related data with source entity.
     * @param bool $nullable Defines if the relation can be nullable (child can have no parent).
     * @param bool $fkCreate Set to true to automatically create FK on innerKey.
     * @param non-empty-string $fkAction FK onDelete and onUpdate action.
     * @param non-empty-string|null $fkOnDelete FK onDelete action. It has higher priority than {@see $fkAction}.
     *        Defaults to {@see $fkAction}.
     * @param bool $indexCreate Create an index on innerKey.
     * @param non-empty-string $load Relation load approach.
     */
    public function __construct(
        string $target,
        protected array|string|null $innerKey = null,
        protected array|string|null $outerKey = null,
        protected bool $cascade = true,
        protected bool $nullable = false,
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
        #[ExpectedValues(values: ['lazy', 'eager'])]
        string $load = 'lazy',
        ?Inverse $inverse = null,
    ) {
        $this->inverse = $inverse;

        parent::__construct($target, $load);
    }
}
