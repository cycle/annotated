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
        protected array|string|null $innerKey = null,
        protected array|string|null $outerKey = null,
        protected bool $cascade = true,
        protected bool $nullable = false,
        protected bool $fkCreate = true,
        /** @Enum({"NO ACTION", "CASCADE", "SET NULL"}) */
        #[ExpectedValues(values: ['NO ACTION', 'CASCADE', 'SET NULL'])]
        protected string $fkAction = 'CASCADE',
        #[ExpectedValues(values: ['NO ACTION', 'CASCADE', 'SET NULL'])]
        protected ?string $fkOnDelete = null,
        protected bool $indexCreate = true,
        #[ExpectedValues(values: ['lazy', 'eager'])]
        string $load = 'lazy',
        ?Inverse $inverse = null
    ) {
        $this->inverse = $inverse;

        parent::__construct($target, $load);
    }
}
