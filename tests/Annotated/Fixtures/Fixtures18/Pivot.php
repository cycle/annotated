<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Fixtures\Fixtures18;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;

#[Entity(
    table: 'Pivotable'
)]
class Pivot
{
    #[Column(type: 'primary', name: 'id_pivot')]
    protected ?int $pid = null;

    #[Column(type: 'bigInteger', name: 'reserv_id_column')]
    private int $booking_bid;

    #[Column(type: 'bigInteger', name: 'booking_reservation_id_column')]
    private int $booking_reservation_rid;
}
