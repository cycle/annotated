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
final class ManyToMany extends Relation
{
    use InverseTrait;

    protected const TYPE = 'manyToMany';

    public function __construct(
        string $target,
        /**
         * Inner key name in source entity. Defaults to a primary key.
         */
        protected array|string|null $innerKey = null,
        /**
         * Outer key name in target entity. Defaults to a primary key.
         */
        protected array|string|null $outerKey = null,
        /**
         * Key name connected to the innerKey of source entity. Defaults to `{sourceRole}_{innerKey}`.
         */
        protected array|string|null $throughInnerKey = null,
        /***
         * Key name connected to the outerKey of a related entity. Defaults to `{targetRole}_{outerKey}`.
         */
        protected array|string|null $throughOuterKey = null,
        /**
         * Automatically save related data with parent entity.
         */
        protected bool $cascade = true,
        /**
         * Defines if the relation can be nullable (child can have no parent).
         */
        protected bool $nullable = false,
        /**
         * Where conditions applied to a related entity.
         */
        protected array $where = [],
        /**
         * Additional sorting rules.
         */
        protected array $orderBy = [],
        /**
         * Pivot entity.
         *
         * @var class-string|null
         */
        protected ?string $through = null,
        /**
         * Where conditions applied to `through` entity.
         */
        protected array $throughWhere = [],
        /**
         * Set to true to automatically create FK on thoughInnerKey and thoughOuterKey.
         */
        protected bool $fkCreate = true,
        /**
         * FK onDelete and onUpdate action.
         *
         * @Enum({"NO ACTION", "CASCADE", "SET NULL"})
         */
        #[ExpectedValues(values: ['NO ACTION', 'CASCADE', 'SET NULL'])]
        protected ?string $fkAction = 'CASCADE',
        /**
         * FK onDelete action. It has higher priority than {@see $fkAction}. Defaults to {@see $fkAction}.
         *
         * @Enum({"NO ACTION", "CASCADE", "SET NULL"})
         */
        #[ExpectedValues(values: ['NO ACTION', 'CASCADE', 'SET NULL'])]
        protected ?string $fkOnDelete = null,
        /**
         * Create index on [thoughInnerKey, thoughOuterKey].
         */
        protected bool $indexCreate = true,
        /**
         * Collection that will contain loaded entities.
         */
        protected ?string $collection = null,
        /**
         * Relation load approach.
         */
        #[ExpectedValues(values: ['lazy', 'eager'])]
        string $load = 'lazy',
        ?Inverse $inverse = null,
        /** @deprecated */
        protected ?string $though = null,
        /** @deprecated */
        protected array|string|null $thoughInnerKey = null,
        /**  @deprecated */
        protected array|string|null $thoughOuterKey = null,
        /** @deprecated */
        protected array|null $thoughWhere = [],
    ) {
        $this->inverse = $inverse;

        parent::__construct($target, $load);
    }
}
