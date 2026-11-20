<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Fixtures\Fixtures26;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Embeddable;

/**
 * @Embeddable(
 *     role="address",
 *     columnPrefix="address_",
 *     typecast={"Cycle\Annotated\Tests\Fixtures\Fixtures26\CityTypecast"}
 * )
 */
#[Embeddable(
    role: 'address',
    columnPrefix: 'address_',
    typecast: [
        CityTypecast::class,
    ],
)]
class Address
{
    /** @Column(type="string", typecast="city") */
    #[Column(type: 'string', typecast: 'city')]
    protected City $city;

    /** @Column(type="string") */
    #[Column(type: 'string')]
    protected $country;

    /** @Column(type="string") */
    #[Column(type: 'string')]
    protected $address;

    /** @Column(type="int") */
    #[Column(type: 'integer')]
    protected $zipcode;
}
