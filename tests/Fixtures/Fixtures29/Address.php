<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Fixtures\Fixtures29;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Embeddable;
use Cycle\Annotated\Annotation\Relation\BelongsTo;

#[Embeddable]
class Address
{
    #[Column(type: 'string')]
    public string $street;

    #[BelongsTo(target: Author::class)]
    public Author $author;
}
