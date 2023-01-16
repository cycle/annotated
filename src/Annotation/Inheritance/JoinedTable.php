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
     * @param non-empty-string|null $fkAction FK onDelete and onUpdate action.
     */
    public function __construct(
        private ?string $outerKey = null,
        private bool $fkCreate = true,
        /**
         * @Enum({"NO ACTION", "CASCADE", "SET NULL"})
         */
        #[ExpectedValues(values: ['NO ACTION', 'CASCADE', 'SET NULL'])]
        private ?string $fkAction = 'CASCADE',
    ) {
        parent::__construct('joined');
    }

    public function getOuterKey(): ?string
    {
        return $this->outerKey;
    }

    public function isCreateFk(): bool
    {
        return $this->fkCreate;
    }

    public function getFkAction(): string
    {
        return $this->fkAction;
    }
}
