<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Fixtures\Fixtures6;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Relation\Embedded;
use Cycle\ORM\Parser\Typecast as DefaultTypecast;

/**
 * @Entity()
 */
#[Entity(
    typecast: [
        CityTypecast::class,
        DefaultTypecast::class,
    ]
)]
class User
{
    /** @Column(type="primary") */
    #[Column(type: 'primary')]
    protected $id;

    /** @Embedded(target=Address::class) */
    #[Embedded(target: Address::class)]
    protected $address;

    /** @Embedded(target=Address::class, prefix="work_address_") */
    #[Embedded(target: Address::class, prefix: 'work_address_')]
    protected Address $workAddress;
}
