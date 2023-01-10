<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Fixtures\Fixtures20;

use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Inheritance\JoinedTable as InheritanceJoinedTable;

/**
 * @Entity
 * @InheritanceJoinedTable(fkCreate=false, outerKey="foo_id"))
 */
#[Entity]
#[InheritanceJoinedTable(fkCreate: false, outerKey: 'foo_id')]
class Buyer extends Person
{
}
