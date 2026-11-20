<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Fixtures\Configurator;

use Cycle\Annotated\Annotation\Relation\Inverse;
use Cycle\Annotated\Annotation\Relation\RelationInterface;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class TargetlessRelation implements RelationInterface
{
    public function getType(): string
    {
        return 'hasOne';
    }

    public function getTarget(): ?string
    {
        return null;
    }

    public function getOptions(): array
    {
        return [];
    }

    public function getInverse(): ?Inverse
    {
        return null;
    }
}
