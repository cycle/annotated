<?php

declare(strict_types=1);

namespace Cycle\Annotated\Annotation\Relation;

use JetBrains\PhpStorm\ExpectedValues;
use Spiral\Attributes\NamedArgumentConstructor;

/**
 * @Annotation
 * @NamedArgumentConstructor
 * @Target("PROPERTY")
 */
#[\Attribute(\Attribute::TARGET_PROPERTY), NamedArgumentConstructor]
class Embedded extends Relation
{
    protected const TYPE = 'embedded';

    protected ?string $embeddedPrefix = null;

    /**
     * @param non-empty-string $target Entity to embed.
     * @param 'eager'|'lazy' $load Relation load approach.
     * @param string|null $prefix Prefix for embedded entity columns.
     */
    public function __construct(
        string $target,
        #[ExpectedValues(values: ['lazy', 'eager'])]
        string $load = 'eager',
        ?string $prefix = null,
    ) {
        $this->embeddedPrefix = $prefix;

        parent::__construct($target, $load);
    }

    public function getInverse(): ?Inverse
    {
        return null;
    }

    public function getPrefix(): ?string
    {
        return $this->embeddedPrefix;
    }

    public function setPrefix(string $prefix): self
    {
        $this->embeddedPrefix = $prefix;

        return $this;
    }
}
