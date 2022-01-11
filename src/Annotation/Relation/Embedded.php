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
    protected ?string $embeddedPrefix = null;

    /**
     * @param non-empty-string $target Entity to embed.
     * @param non-empty-string $load Relation load approach.
     * @param non-empty-string|null $prefix Prefix for embedded entity columns.
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
