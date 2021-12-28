<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Fixtures\Fixtures16;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Inheritance\JoinedTable as InheritanceJoinedTable;

/**
 * @Entity
 * @InheritanceJoinedTable
 */
#[Entity]
#[InheritanceJoinedTable]
class Supplier extends Person
{
    /** @Column(type="integer") */
    #[Column(type: 'integer')]
    private int $custom_id;
}
