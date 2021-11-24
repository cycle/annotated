<?php

declare(strict_types=1);

namespace Cycle\Annotated\Annotation\Relation\Morphed;

use Cycle\Annotated\Annotation\Relation\Inverse;
use Cycle\Annotated\Annotation\Relation\Relation;
use Cycle\Annotated\Annotation\Relation\Traits\InverseTrait;
use Doctrine\Common\Annotations\Annotation\NamedArgumentConstructor;
use JetBrains\PhpStorm\ExpectedValues;

/**
 * @Annotation
 * @NamedArgumentConstructor
 * @Target("PROPERTY")
 */
#[\Attribute(\Attribute::TARGET_PROPERTY), NamedArgumentConstructor]
final class MorphedHasMany extends Relation
{
    use InverseTrait;

    protected const TYPE = 'morphedHasMany';

    public function __construct(
        string $target,
        protected bool $cascade = true,
        protected bool $nullable = false,
        protected array|string|null $innerKey = null,
        protected array|string|null $outerKey = null,
        protected string $morphKey = '{relationName}_role',
        protected int $morphKeyLength = 32,
        protected array $where = [],
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
