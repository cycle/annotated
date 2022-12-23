<?php

declare(strict_types=1);

namespace Cycle\Annotated\Locator;

interface EntityLocatorInterface
{
    /**
     * @return Entity[]
     */
    public function getEntities(): array;
}
