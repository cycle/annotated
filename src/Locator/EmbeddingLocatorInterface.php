<?php

declare(strict_types=1);

namespace Cycle\Annotated\Locator;

interface EmbeddingLocatorInterface
{
    /**
     * @return Embedding[]
     */
    public function getEmbeddings(): array;
}
