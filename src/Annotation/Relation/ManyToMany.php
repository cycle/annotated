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
        protected array|string|null $innerKey = null,
        protected array|string|null $outerKey = null,
        protected array|string|null $throughInnerKey = null,
        protected array|string|null $throughOuterKey = null,
        protected bool $cascade = true,
        protected bool $nullable = false,
        protected array $where = [],
        protected array $orderBy = [],
        protected ?string $through = null,
        protected array $throughWhere = [],
        protected bool $fkCreate = true,
        /** @Enum({"NO ACTION", "CASCADE", "SET NULL"}) */
        #[ExpectedValues(values: ['NO ACTION', 'CASCADE', 'SET NULL'])]
        protected string $fkAction = 'SET NULL',
        /** @Enum({"NO ACTION", "CASCADE", "SET NULL"}) */
        #[ExpectedValues(values: ['NO ACTION', 'CASCADE', 'SET NULL'])]
        protected ?string $fkOnDelete = null,
        protected bool $indexCreate = true,
        protected ?string $collection = null,
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
