<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Fixtures\Fixtures29;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\ForeignKey;

#[Entity(role: 'book')]
#[ForeignKey(target: Author::class, innerKey: ['authorId', 'authorKind'], outerKey: ['id', 'kind'])]
class Book
{
    #[Column(type: 'primary')]
    public int $id;

    #[Column(type: 'integer')]
    public int $authorId;

    #[Column(type: 'string')]
    public string $authorKind;
}
