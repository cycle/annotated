<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Fixtures\Fixtures21;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Inheritance\JoinedTable;

/**
 * @Entity
 * @JoinedTable
 */
#[Entity]
#[JoinedTable]
class Buyer extends Person
{
    /** @Column(type="string") */
    #[Column(type: 'string')]
    public string $foo;

    /** @Column(type="string") */
    #[Column(type: 'string')]
    public string $bar;
}
