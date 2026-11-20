<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Fixtures\ForeignKey;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;

#[Entity(role: 'customer')]
class Customer
{
    #[Column(type: 'primary', name: 'customer_id')]
    public int $id;
}
