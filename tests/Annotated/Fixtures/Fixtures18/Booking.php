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
    protected ?int $id = null;

    #[Column(type: 'string(12)')]
    protected ?string $code = null;

    #[Column(type: 'bigInteger', name: 'reserv_id_column')]
    private int $reserv_id;

    #[HasOne(target: Reservation::class, innerKey: 'reserv_id', outerKey: 'id', fkCreate: false)]
    protected Reservation $reservation;
}
