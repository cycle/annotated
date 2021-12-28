<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Fixtures\Fixtures18;

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

    // Without manual declaration
    #[HasOne(target: Reservation::class, fkCreate: false)]
    protected Reservation $reservation0;

    // Use property names
    #[HasOne(target: Reservation::class, innerKey: 'reserv_id', outerKey: 'rid', fkCreate: false)]
    protected Reservation $reservation1;

    // Use column names
    #[HasOne(target: Reservation::class, innerKey: 'reserv_id_column', outerKey: 'id_reservation', fkCreate: false)]
    protected Reservation $reservation2;

    // Without manual declaration
    #[ManyToMany(target: Reservation::class, through: Pivot::class, fkCreate: false)]
    protected array $reservations0;

    // Use property names
    #[ManyToMany(
        target: Reservation::class,
        through: Pivot::class,
        innerKey: 'reserv_id',
        outerKey: 'booking_id',
        throughInnerKey: 'booking_reservation_rid',
        throughOuterKey: 'booking_bid',
        fkCreate: false,
    )]
    protected array $reservations1;

    // Use column names
    #[ManyToMany(
        target: Reservation::class,
        through: Pivot::class,
        innerKey: 'reserv_id_column',
        outerKey: 'booking_id_column',
        throughInnerKey: 'booking_reservation_id_column',
        throughOuterKey: 'reserv_id_column',
        fkCreate: false,
    )]
    protected array $reservations2;
}
