<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Fixtures\ForeignKey;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\ForeignKey;

#[Entity(role: 'order')]
#[ForeignKey(target: Customer::class, innerKey: 'customerId')]
class Order
{
    #[Column(type: 'primary')]
    public int $id;

    #[Column(type: 'integer', name: 'customer_ref')]
    public int $customerId;
}
