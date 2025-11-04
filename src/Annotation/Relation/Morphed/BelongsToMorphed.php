<?php

declare(strict_types=1);

namespace Cycle\Annotated\Annotation\Relation\Morphed;

use Cycle\Annotated\Annotation\Relation\Inverse;
use Cycle\Annotated\Annotation\Relation\Relation;
use Cycle\Annotated\Annotation\Relation\Traits\InverseTrait;
use Doctrine\Common\Annotations\Annotation\Target;
use JetBrains\PhpStorm\ExpectedValues;
use Spiral\Attributes\NamedArgumentConstructor;

/**
 * @Annotation
 * @NamedArgumentConstructor
 * @Target("PROPERTY")
 */
#[\Attribute(\Attribute::TARGET_PROPERTY), NamedArgumentConstructor]
class BelongsToMorphed extends Relation
{
    use InverseTrait;

    protected const TYPE = 'belongsToMorphed';

    /**
     * @param non-empty-string $target
     * @param bool $cascade Automatically save related data with source entity.
     * @param bool $nullable Defines if the relation can be nullable (child can have no parent).
     * @param array|non-empty-string|null $innerKey Inner key in source entity. Defaults to `{relationName}_{outerKey}`.
     * @param array|non-empty-string|null $outerKey Outer key in the related entity. Defaults to primary key.
     * @param non-empty-string|null $morphKey Name of key to store related entity role. Defaults to `{relationName}_role`.
     * @param int $morphKeyLength The length of morph key.
     * @param bool $indexCreate Create an index on morphKey and innerKey.
     * @param non-empty-string $load Relation load approach.
     */
    public function __construct(
        string $target,
        protected bool $cascade = true,
        protected bool $nullable = true,
        protected array|string|null $innerKey = null,
        protected array|string|null $outerKey = null,
        protected ?string $morphKey = null,
        protected int $morphKeyLength = 32,
        protected bool $indexCreate = true,
        #[ExpectedValues(values: ['lazy', 'eager'])]
        string $load = 'lazy',
        ?Inverse $inverse = null,
    ) {
        $this->inverse = $inverse;

        parent::__construct($target, $load);
    }
}
