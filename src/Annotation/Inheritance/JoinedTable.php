<?php

declare(strict_types=1);

namespace Cycle\Annotated\Annotation\Inheritance;

use Cycle\Annotated\Annotation\Inheritance;
use JetBrains\PhpStorm\ExpectedValues;
use Spiral\Attributes\NamedArgumentConstructor;

/**
 * @Annotation
 * @NamedArgumentConstructor
 * @Target("CLASS")
 */
#[\Attribute(\Attribute::TARGET_CLASS), NamedArgumentConstructor]
class JoinedTable extends Inheritance
{
    /**
     * @param non-empty-string|null $outerKey Outer (parent) key name.
     * @param bool $fkCreate Set to true to automatically create FK on outerKey.
     * @param non-empty-string $fkAction FK onDelete and onUpdate action.
     */
    public function __construct(
        protected ?string $outerKey = null,
        protected bool $fkCreate = true,
        /**
         * @Enum({"NO ACTION", "CASCADE", "SET NULL"})
         */
        #[ExpectedValues(values: ['NO ACTION', 'CASCADE', 'SET NULL'])]
        protected string $fkAction = 'CASCADE',
    ) {
        parent::__construct('joined');
    }

    /**
     * @return ?non-empty-string
     */
    public function getOuterKey(): ?string
    {
        return $this->outerKey;
    }

    public function isCreateFk(): bool
    {
        return $this->fkCreate;
    }

    /**
     * @return non-empty-string
     */
    public function getFkAction(): string
    {
        return $this->fkAction;
    }
}
