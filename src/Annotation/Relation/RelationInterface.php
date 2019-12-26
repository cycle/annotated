<?php

/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

declare(strict_types=1);

namespace Cycle\Annotated\Annotation\Relation;

interface RelationInterface
{
    /**
     * @return string
     */
    public function getType(): string;

    /**
     * @return string|null
     */
    public function getTarget(): ?string;

    /**
     * @return array
     */
    public function getOptions(): array;

    /**
     * @return Inverse|null
     */
    public function getInverse(): ?Inverse;
}
