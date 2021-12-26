<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Fixtures\Fixtures18;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Relation\HasOne;

#[Entity(
    table: 'FlightBooking'
)]
class Booking
{
    #[Column(type: 'primary', name: 'id_booking')]
    protected ?int $bid = null;

    #[Column(type: 'bigInteger', name: 'reserv_id_column')]
    private int $reserv_id;

    // Without manual declaration
    #[HasOne(target: Reservation::class, fkCreate: false)]
    protected Reservation $reservation0;

    // Use property names
    #[HasOne(target: Reservation::class, innerKey: 'reserv_id', outerKey: 'rid', fkCreate: false)]
    protected Reservation $reservation1;

    // Use column names
    #[HasOne(target: Reservation::class, innerKey: 'reserv_id_column', outerKey: 'id_reservation', fkCreate: false)]
    protected Reservation $reservation2;

    // Virtual entity fields
    #[HasOne(target: Reservation::class, innerKey: 'undefined_field_has_one1', outerKey: 'undefined_field_has_one2')]
    protected Reservation $reservation3;
}
