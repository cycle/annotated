<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Fixtures\Fixtures27;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Inheritance\DiscriminatorColumn;
use Cycle\Annotated\Annotation\Relation\BelongsTo;

#[Entity]
#[DiscriminatorColumn(name: 'type')]
class BtParent
{
    #[Column(type: 'primary', name: 'id')]
    public int $id;

    #[Column(type: 'string')]
    public string $type;

    #[BelongsTo(target: BtTarget::class, nullable: true, fkCreate: false)]
    public ?BtTarget $target = null;
}
