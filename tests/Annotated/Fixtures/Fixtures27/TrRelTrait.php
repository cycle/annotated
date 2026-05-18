<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Fixtures\Fixtures27;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Relation\HasOne;

trait TrRelTrait
{
    #[Column(type: 'int', nullable: true)]
    public ?int $target_id = null;

    #[HasOne(target: TrTarget::class, innerKey: 'target_id', outerKey: 'id', nullable: true)]
    public ?TrTarget $target = null;
}
