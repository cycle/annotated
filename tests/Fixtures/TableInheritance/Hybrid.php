<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Fixtures\TableInheritance;

use Cycle\Annotated\Annotation\Inheritance;

/**
 * An inheritance type that only a custom generator knows how to handle.
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
final class Hybrid extends Inheritance
{
    public function __construct()
    {
        parent::__construct('hybrid');
    }
}
