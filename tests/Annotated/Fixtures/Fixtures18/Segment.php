<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Fixtures\Fixtures18;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Relation\BelongsTo;

#[Entity(
    table: 'FlightSegment'
)]
class Segment
{
    #[Column(name: 'flightSegmentId', type: 'primary')]
    protected ?int $id = null;

    #[BelongsTo(target: Reservation::class, innerKey: 'FlightReservation_flightReservationId', outerKey: 'flightReservationId')]
    private Reservation $flightReservation;

}
