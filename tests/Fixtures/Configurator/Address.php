<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Fixtures\Configurator;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Embeddable;

#[Embeddable(role: 'postal', mapper: 'AddressMapper', typecast: ['Typecaster', 'Sub/Typecaster'])]
class Address
{
    #[Column(type: 'string')]
    public string $street;
}
