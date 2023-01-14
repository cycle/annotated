<?php

declare(strict_types=1);

namespace Annotated\Fixtures\Fixtures21;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Inheritance\SingleTable as InheritanceSingleTable;
use Cycle\Annotated\Tests\Fixtures\Fixtures20\Person;

/**
 * @Entity
 * @InheritanceSingleTable
 */
#[Entity]
#[InheritanceSingleTable]
class Employee extends Person
{
    /** @Column(type="int") */
    #[Column(type: 'int')]
    public int $salary;
}
