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
class Executive extends Employee
{
    /** @Column(type="integer") */
    #[Column(type: 'integer')]
    public int $bonus;
}
