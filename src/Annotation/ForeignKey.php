<?php

declare(strict_types=1);

namespace Cycle\Annotated\Annotation;

use Doctrine\Common\Annotations\Annotation\Target;
use JetBrains\PhpStorm\ExpectedValues;
use Spiral\Attributes\NamedArgumentConstructor;

/**
 * @Annotation
 *
 * @NamedArgumentConstructor
 *
 * @Target({"PROPERTY", "ANNOTATION", "CLASS"})
 */
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_CLASS | \Attribute::IS_REPEATABLE)]
#[NamedArgumentConstructor]
class ForeignKey
{
    public function __construct(
        public string $target,
        protected array|string $outerKey,
        protected array|string|null $innerKey = null,
        /**
         * @Enum({"NO ACTION", "CASCADE", "SET NULL"})
         */
        #[ExpectedValues(values: ['NO ACTION', 'CASCADE', 'SET NULL'])]
        public string $action = 'CASCADE',
        public bool $indexCreate = true,
    ) {
    }

    public function getOuterKey(): array
    {
        return (array) $this->outerKey;
    }

    public function getInnerKey(): ?array
    {
        return $this->innerKey === null ? null : (array) $this->innerKey;
    }
}