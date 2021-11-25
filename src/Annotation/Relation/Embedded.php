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

    /**
     * @param non-empty-string $target Entity to embed.
     * @param non-empty-string $load Relation load approach.
     */
    public function __construct(
        string $target,
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
