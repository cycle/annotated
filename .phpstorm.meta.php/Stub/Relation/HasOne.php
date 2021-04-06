<?php

/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

declare(strict_types=1);

namespace Cycle\Annotated\Annotation\Relation;

use JetBrains\PhpStorm\ExpectedValues;

final class HasOne
{
    public function __construct(
        string $target,
        bool $cascade = true,
        bool $nullable = false,
        string $innerKey = null,
        string $outerKey = '{parentRole}_{innerKey}',
        bool $fkCreate = true,
        #[ExpectedValues(values: ['NO ACTION', 'CASCADE', 'SET NULL'])]
        string $fkAction = 'CASCADE',
        bool $indexCreate = true,
        #[ExpectedValues(values: ['lazy', 'eager'])]
        string $load = 'lazy',
        // Inverse $inverse = null, // can be uncommented for compatibility with php 8.1
    ) {
    }
}
