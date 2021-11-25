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
        /**
         * Automatically save related data with source entity. Defaults to `true`
         */
        protected bool $cascade = true,
        /**
         * Defines if the relation can be nullable (child can have no parent). Defaults to `true`
         */
        protected bool $nullable = true,
        /**
         * Inner key in source entity. Defaults to `{relationName}_{outerKey}`
         */
        protected array|string|null $innerKey = null,
        /**
         * Outer key in the related entity. Defaults to primary key
         */
        protected array|string|null $outerKey = null,
        /**
         * Name of key to store related entity role. Defaults to `{relationName}_role`
         */
        protected string $morphKey = '{relationName}_role',
        /**
         * The length of morph key. Defaults to 32
         */
        protected int $morphKeyLength = 32,
        /**
         * Create an index on morphKey and innerKey. Defaults to `true`
         */
        protected bool $indexCreate = true,
        /**
         * Relation load approach. Defaults to `lazy`
         */
        #[ExpectedValues(values: ['lazy', 'eager'])]
        string $load = 'lazy',
        ?Inverse $inverse = null
    ) {
        $this->inverse = $inverse;

        parent::__construct($target, $load);
    }
}
