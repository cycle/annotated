<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Fixtures\Fixtures19;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Relation\HasOne;
use Cycle\Annotated\Annotation\Relation\ManyToMany;

#[Entity(
    table: 'FlightBooking'
)]
class Booking
{
    #[Column(type: 'primary', name: 'id_booking')]
    protected ?int $bid = null;

    #[Column(type: 'bigInteger', name: 'reserv_id_column')]
    private int $reserv_id;

    // Virtual entity fields
    #[HasOne(target: Reservation::class, innerKey: 'undefined_field_has_one1', outerKey: 'undefined_field_has_one2')]
    protected Reservation $reservation3;

    // Virtual entity fields
    #[ManyToMany(
        target: Reservation::class,
        through: Pivot::class,
        innerKey: 'undefined_field_mtm1',
        outerKey: 'undefined_field_mtm2',
        throughInnerKey: 'undefined_field_mtm3',
        throughOuterKey: 'undefined_field_mtm4',
    )]
    protected array $reservations3;
}
