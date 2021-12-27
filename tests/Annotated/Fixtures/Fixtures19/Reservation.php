<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Fixtures\Fixtures19;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Relation\HasMany;

#[Entity(
    role: 'booking_reservation',
    table: 'FlightReservation'
)]
class Reservation
{
    #[Column(type: 'primary', name: 'id_reservation')]
    protected ?int $rid = null;

    #[Column(type: 'bigInteger', name: 'booking_id_column')]
    private int $booking_id;

    // Virtual entity fields
    #[HasMany(target: Segment::class, innerKey: 'undefined_field_has_many1', outerKey: 'undefined_field_has_many2')]
    private array $segments3 = [];
}
