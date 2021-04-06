<?php

declare(strict_types=1);

namespace Cycle\Annotated\Annotation\Relation;

use JetBrains\PhpStorm\ExpectedValues;

final class Embedded
{
    public function __construct(
        string $target,
        #[ExpectedValues(values: ['lazy', 'eager'])]
        string $load = 'eager',
    ) {
    }
}
