<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Fixtures\Fixtures20;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Inheritance\SingleTable as InheritanceSingleTable;

/**
 * @Entity
 * @InheritanceSingleTable
 */
#[Entity]
#[InheritanceSingleTable]
class Ceo extends Employee
{
    /** @Column(type="int") */
    #[Column(type: 'int')]
    public int $stocks;
}
