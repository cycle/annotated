<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Fixtures\Fixtures29;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\ForeignKey;

#[Entity(role: 'review')]
#[ForeignKey(target: Author::class, innerKey: 'missing')]
class Review
{
    #[Column(type: 'primary')]
    public int $id;
}
