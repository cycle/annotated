<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Fixtures16;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Inheritance\SingleTable as InheritanceSingleTable;

/**
 * @Entity
 * @InheritanceSingleTable
 */
#[Entity]
#[InheritanceSingleTable]
class Employee extends Person
{
    use FooColumns;

    /** @Column(type="int") */
    #[Column(type: 'int')]
    protected int $salary;
}
