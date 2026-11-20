<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Fixtures\Fixtures27;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Inheritance\JoinedTable;

#[Entity]
#[JoinedTable]
class BtJoined extends BtParent
{
    #[Column(type: 'int')]
    public int $bonus;
}
