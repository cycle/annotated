<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Fixtures\Configurator;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\ForeignKey;

#[Entity]
class Draft
{
    #[Column(type: 'primary')]
    public int $id;

    #[Column(type: 'integer')]
    #[ForeignKey(target: Author::class)]
    public int $editorId;

    #[TargetlessRelation]
    public ?Author $reviewer = null;
}
