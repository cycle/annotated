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
        /**
         * Automatically save related data with parent entity. Defaults to `true`
         */
        protected bool $cascade = true,
        /**
         * Defines if the relation can be nullable (child can have no parent). Defaults to `false`
         */
        protected bool $nullable = false,
        /**
         * Inner key in parent entity. Defaults to the primary key
         */
        protected array|string|null $innerKey = null,
        /**
         * Outer key name. Defaults to `{parentRole}_{innerKey}`
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
         * Additional where condition to be applied for the relation. Defaults to none.
         */
        protected array $where = [],
        /**
         * Create an index on morphKey and innerKey. Defaults to `true`
         */
        protected bool $indexCreate = true,
        /**
         * Collection that will contain loaded entities. Defaults to `array`
         */
        protected ?string $collection = null,
        /**
         * Relation load approach. Defaults to `lazy`
         */
        #[ExpectedValues(values: ['lazy', 'eager'])]
        string $load = 'lazy',
        ?Inverse $inverse = null,
    ) {
        $this->inverse = $inverse;

        parent::__construct($target, $load);
    }
}
