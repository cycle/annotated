<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Fixtures\Fixtures18;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;

#[Entity(
    table: 'FlightSegment'
)]
class Segment
{
    #[Column(name: 'id_segment', type: 'primary')]
    protected ?int $sid = null;

    #[Column(name: 'parent_id_column', type: 'bigInteger')]
    protected ?int $parent_id = null;
}
