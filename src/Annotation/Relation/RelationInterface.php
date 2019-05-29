<?php declare(strict_types=1);
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Cycle\Annotated\Annotation\Relation;

interface RelationInterface
{
    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @return string|null
     */
    public function getTarget(): ?string;

    /**
     * @return array
     */
    public function getOptions(): array;

    /**
     * @return bool
     */
    public function isInversed(): bool;

    /**
     * @return string
     */
    public function getInverseType(): string;

    /**
     * @return string
     */
    public function getInverseName(): string;

    /**
     * @return int|null
     */
    public function getInverseLoadMethod(): ?int;
}