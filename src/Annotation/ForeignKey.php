<?php

declare(strict_types=1);

namespace Cycle\Annotated\Annotation;

use Doctrine\Common\Annotations\Annotation\Target;
use JetBrains\PhpStorm\ExpectedValues;
use Spiral\Attributes\NamedArgumentConstructor;

/**
 * @Annotation
 * @NamedArgumentConstructor
 * @Target({"PROPERTY", "ANNOTATION", "CLASS"})
 */
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_CLASS | \Attribute::IS_REPEATABLE)]
#[NamedArgumentConstructor]
class ForeignKey
{
    /**
     * @param non-empty-string $target Role or class name of the target entity.
     * @param list<non-empty-string>|non-empty-string|null $innerKey You don't need to specify this if the attribute
     *        is used on a property.
     * @param list<non-empty-string>|non-empty-string|null $outerKey Outer key in the target entity.
     *        Defaults to the primary key.
     * @param 'CASCADE'|'NO ACTION'|'SET null' $action
     * @param bool $indexCreate Note: MySQL and MSSQL might create an index for the foreign key automatically.
     */
    public function __construct(
        public string $target,
        public array|string|null $innerKey = null,
        public array|string|null $outerKey = null,
        /**
         * @Enum({"NO ACTION", "CASCADE", "SET NULL"})
         */
        #[ExpectedValues(values: ['NO ACTION', 'CASCADE', 'SET NULL'])]
        public string $action = 'CASCADE',
        public bool $indexCreate = true,
    ) {}
}
