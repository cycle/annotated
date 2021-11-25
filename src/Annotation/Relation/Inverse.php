<?php

declare(strict_types=1);

namespace Cycle\Annotated\Annotation\Relation;

use Cycle\ORM\Relation;
use Doctrine\Common\Annotations\Annotation\Enum;
use Doctrine\Common\Annotations\Annotation\NamedArgumentConstructor;
use JetBrains\PhpStorm\ExpectedValues;

/**
 * @Annotation
 * @NamedArgumentConstructor
 * @Target({"PROPERTY", "ANNOTATION"})
 */
#[\Attribute(\Attribute::TARGET_PROPERTY), NamedArgumentConstructor]
final class Inverse
{
    public function __construct(
        /**
         * Columns name that will represent relation
         */
        private string $as,
        /**
         * Relation type
         *
         * @Enum({"hasOne", "belongsTo", "embedded", "hasMany", "manyToMany", "refersTo"}
         */
        #[ExpectedValues(values: ['hasOne', 'belongsTo', 'embedded', 'hasMany', 'manyToMany', 'refersTo'])]
        private string $type,
        /**
         * Relation load approach. Defaults to `lazy`
         *
         * @Enum({"eager", "lazy", "promise"}
         */
        #[ExpectedValues(values: ['eager', 'lazy', 'promise'])]
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
