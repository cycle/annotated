<?php

declare(strict_types=1);

namespace Cycle\Annotated\Annotation\Relation;

use Doctrine\Common\Annotations\Annotation\NamedArgumentConstructor;
use JetBrains\PhpStorm\ExpectedValues;

/**
 * @Annotation
 * @NamedArgumentConstructor
 * @Target("PROPERTY")
 */
#[\Attribute(\Attribute::TARGET_PROPERTY), NamedArgumentConstructor]
final class Embedded extends Relation
{
    protected const TYPE = 'embedded';

    public function __construct(
        /**
         * Entity to embed.
         *
         * @var non-empty-string
         */
        string $target,
        /**
         * Relation load approach.
         *
         * @var non-empty-string
         */
        #[ExpectedValues(values: ['lazy', 'eager'])]
        string $load = 'eager'
    ) {
        parent::__construct($target, $load);
    }

    public function getInverse(): ?Inverse
    {
        return null;
    }
}
