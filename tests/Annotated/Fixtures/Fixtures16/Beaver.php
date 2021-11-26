<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Fixtures\Fixtures16;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;

/** @Entity */
#[Entity]
class Beaver extends Person
{
    use ExtraColumns;

    /** @Column(type="int") */
    #[Column(type: 'int')]
    protected int $teethAmount;
}
