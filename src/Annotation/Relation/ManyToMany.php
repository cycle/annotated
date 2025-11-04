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
class ManyToMany extends Relation
{
    use InverseTrait;

    protected const TYPE = 'manyToMany';

    /**
     * @param non-empty-string $target
     * @param array|non-empty-string|null $innerKey Inner key name in source entity. Defaults to a primary key.
     * @param array|non-empty-string|null $outerKey Outer key name in target entity. Defaults to a primary key.
     * @param array|non-empty-string|null $throughInnerKey Key name connected to the innerKey of source entity.
     *        Defaults to `{sourceRole}_{innerKey}`.
     * @param array|non-empty-string|null $throughOuterKey Key name connected to the outerKey of a related entity.
     *        Defaults to `{targetRole}_{outerKey}`.
     * @param bool $cascade Automatically save related data with parent entity.
     * @param bool $nullable Defines if the relation can be nullable (child can have no parent).
     * @param array $where Where conditions applied to a related entity.
     * @param array $orderBy Additional sorting rules.
     * @param class-string|non-empty-string $through Pivot entity.
     * @param array $throughWhere Where conditions applied to `through` entity.
     * @param bool $fkCreate Set to true to automatically create FK on throughInnerKey and throughOuterKey.
     * @param non-empty-string $fkAction FK onDelete and onUpdate action.
     * @param non-empty-string|null $fkOnDelete FK onDelete action. It has higher priority than {@see $fkAction}.
     *        Defaults to {@see $fkAction}.
     * @param bool $indexCreate Create index on [throughInnerKey, throughOuterKey].
     * @param non-empty-string|null $collection Collection that will contain loaded entities.
     * @param non-empty-string $load Relation load approach.
     */
    public function __construct(
        string $target,
        protected string $through,
        protected array|string|null $innerKey = null,
        protected array|string|null $outerKey = null,
        protected array|string|null $throughInnerKey = null,
        protected array|string|null $throughOuterKey = null,
        protected bool $cascade = true,
        protected bool $nullable = false,
        protected array $where = [],
        protected array $orderBy = [],
        protected array $throughWhere = [],
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
