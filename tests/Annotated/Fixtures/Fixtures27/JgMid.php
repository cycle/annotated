<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Fixtures\Fixtures27;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Inheritance\JoinedTable;
use Cycle\Annotated\Annotation\Relation\HasOne;

#[Entity]
#[JoinedTable]
class JgMid extends JgParent
{
    #[Column(type: 'int', nullable: true)]
    public ?int $target_id = null;

    #[HasOne(target: JgTarget::class, innerKey: 'target_id', outerKey: 'id', nullable: true)]
    public ?JgTarget $target = null;
}
