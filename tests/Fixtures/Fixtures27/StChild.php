<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Fixtures\Fixtures27;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Inheritance\SingleTable;
use Cycle\Annotated\Annotation\Relation\HasOne;

#[Entity]
#[SingleTable]
class StChild extends StParent
{
    #[Column(type: 'int', nullable: true)]
    public ?int $target_id = null;

    #[HasOne(target: StTarget::class, innerKey: 'target_id', outerKey: 'id', nullable: true, fkCreate: false)]
    public ?StTarget $target = null;
}
