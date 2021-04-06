<?php

declare(strict_types=1);

namespace Cycle\Annotated\Annotation\Relation;

use JetBrains\PhpStorm\ExpectedValues;

final class Inverse
{
    public function __construct(
        string $as,
        string $type,
        #[ExpectedValues(values: ['eager', 'lazy', 'promise'])]
        string $load,
    ) {
    }
}
