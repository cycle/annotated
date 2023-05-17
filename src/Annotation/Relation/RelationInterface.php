<?php

declare(strict_types=1);

namespace Cycle\Annotated\Annotation\Relation;

interface RelationInterface
{
    /**
     * @return non-empty-string
     */
    public function getType(): string;

    /**
     * @return non-empty-string|null
     */
    public function getTarget(): ?string;

    /**
     * @return array<string, mixed>
     */
    public function getOptions(): array;

    public function getInverse(): ?Inverse;
}
