<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Fixtures\Fixtures18;

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

    // #[BelongsTo(target: Booking::class, innerKey: 'id', outerKey: 'FlightRezervationId', fkCreate: false)]
    // private ?Booking $booking = null;

    // Without manual declaration
    #[HasMany(target: Segment::class, fkCreate: false)]
    private array $segments0 = [];

    // Use property names
    #[HasMany(target: Segment::class, innerKey: 'rid', outerKey: 'parent_id', fkCreate: false)]
    private array $segments1 = [];

    // Use column names
    #[HasMany(target: Segment::class, innerKey: 'id_reservation', outerKey: 'parent_id_column', fkCreate: false)]
    private array $segments2 = [];
}
