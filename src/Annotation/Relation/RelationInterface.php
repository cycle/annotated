<?php

declare(strict_types=1);

namespace Cycle\Annotated\Annotation\Relation;

interface RelationInterface
{
    public function getType(): string;

    public function getTarget(): ?string;

    /**
     * @return array<string, mixed>
     */
    public function getOptions(): array;

    public function getInverse(): ?Inverse;
}
