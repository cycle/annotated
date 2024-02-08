<?php

declare(strict_types=1);

namespace Cycle\Annotated\Annotation\Relation;

use Cycle\ORM\Relation;
use Doctrine\Common\Annotations\Annotation\Enum;
use JetBrains\PhpStorm\ExpectedValues;
use Spiral\Attributes\NamedArgumentConstructor;

/**
 * @Annotation
 * @NamedArgumentConstructor
 * @Target({"PROPERTY", "ANNOTATION"})
 */
#[\Attribute(\Attribute::TARGET_PROPERTY), NamedArgumentConstructor]
final class Inverse
{
    /**
     * @param non-empty-string $as Columns name that will represent relation
     * @param non-empty-string $type Relation type.
     * @param int|non-empty-string|null $load Relation load approach.
     */
    public function __construct(
        private string $as,
        /**
         * @Enum({"hasOne", "belongsTo", "embedded", "hasMany", "manyToMany", "refersTo"}
         */
        #[ExpectedValues(values: ['hasOne', 'belongsTo', 'embedded', 'hasMany', 'manyToMany', 'refersTo'])]
        private string $type,
        /**
         * @Enum({"eager", "lazy", "promise"}
         */
        #[ExpectedValues(values: ['eager', 'lazy', 'promise', Relation::LOAD_EAGER, Relation::LOAD_PROMISE])]
        private string|int|null $load = null,
    ) {
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getName(): string
    {
        return $this->as;
    }

    public function getLoadMethod(): ?int
    {
        return match ($this->load) {
            'eager', Relation::LOAD_EAGER => Relation::LOAD_EAGER,
            'promise', 'lazy', Relation::LOAD_PROMISE => Relation::LOAD_PROMISE,
            default => null
        };
    }
}
