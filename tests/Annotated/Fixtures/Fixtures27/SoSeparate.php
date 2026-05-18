<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Fixtures\Fixtures27;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;

#[Entity]
class SoSeparate extends SoSti
{
    #[Column(type: 'string')]
    public string $extra;
}
