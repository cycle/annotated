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
    #[Column(type: 'primary', name: 'id')]
    protected ?int $id = null;

    #[Column(type: 'string(12)')]
    protected ?string $code = null;

    #[Column(type: 'bigInteger', name: 'FlightRezervationId_column')]
    private int $FlightRezervationId;

    #[HasOne(target: Reservation::class, innerKey: 'FlightRezervationId', outerKey: 'id', fkCreate: false)]
    protected Reservation $reservation;
}
