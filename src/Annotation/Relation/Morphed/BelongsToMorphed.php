<?php

declare(strict_types=1);

namespace Cycle\Annotated\Annotation\Relation\Morphed;

use Cycle\Annotated\Annotation\Relation\Inverse;
use Cycle\Annotated\Annotation\Relation\Relation;
use Cycle\Annotated\Annotation\Relation\Traits\InverseTrait;
use Doctrine\Common\Annotations\Annotation\NamedArgumentConstructor;
use Doctrine\Common\Annotations\Annotation\Target;
use JetBrains\PhpStorm\ExpectedValues;

/**
 * @Annotation
 * @NamedArgumentConstructor
 * @Target("PROPERTY")
 */
#[\Attribute(\Attribute::TARGET_PROPERTY), NamedArgumentConstructor]
final class BelongsToMorphed extends Relation
{
    use InverseTrait;

    protected const TYPE = 'belongsToMorphed';

    public function __construct(
        string $target,
        protected bool $cascade = true,
        protected bool $nullable = true,
        protected array|string|null $innerKey = null,
        protected array|string|null $outerKey = null,
        protected string $morphKey = '{relationName}_role',
        protected int $morphKeyLength = 32,
        protected bool $indexCreate = true,
        #[ExpectedValues(values: ['lazy', 'eager'])]
        string $load = 'lazy',
        ?Inverse $inverse = null
    ) {
        $this->inverse = $inverse;

        parent::__construct($target, $load);
    }
}
