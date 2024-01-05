<?php

declare(strict_types=1);

namespace Cycle\Annotated\Annotation\Relation\Morphed;

use Cycle\Annotated\Annotation\Relation\Inverse;
use Cycle\Annotated\Annotation\Relation\Relation;
use Cycle\Annotated\Annotation\Relation\Traits\InverseTrait;
use JetBrains\PhpStorm\ExpectedValues;
use Spiral\Attributes\NamedArgumentConstructor;

/**
 * @Annotation
 * @NamedArgumentConstructor
 * @Target("PROPERTY")
 */
#[\Attribute(\Attribute::TARGET_PROPERTY), NamedArgumentConstructor]
class MorphedHasMany extends Relation
{
    use InverseTrait;

    protected const TYPE = 'morphedHasMany';

    /**
     * @param non-empty-string $target
     * @param bool $cascade Automatically save related data with parent entity.
     * @param bool $nullable Defines if the relation can be nullable (child can have no parent).
     * @param array|non-empty-string|null $innerKey Inner key in parent entity. Defaults to the primary key.
     * @param array|non-empty-string|null $outerKey Outer key name. Defaults to `{parentRole}_{innerKey}`.
     * @param non-empty-string|null $morphKey Name of key to store related entity role. Defaults to `{relationName}_role`.
     * @param int $morphKeyLength The length of morph key.
     * @param array $where Additional where condition to be applied for the relation.
     * @param bool $indexCreate Create an index on morphKey and innerKey.
     * @param non-empty-string|null $collection Collection that will contain loaded entities.
     * @param non-empty-string $load Relation load approach.
     */
    public function __construct(
        string $target,
        protected bool $cascade = true,
        protected bool $nullable = false,
        protected array|string|null $innerKey = null,
        protected array|string|null $outerKey = null,
        protected ?string $morphKey = null,
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
