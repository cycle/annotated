<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Fixtures\Fixtures29;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;

#[Entity(role: 'vehicle', database: 'secondary')]
class Vehicle
{
    #[Column(type: 'primary')]
    public int $id;
}
