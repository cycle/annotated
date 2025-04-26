<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Fixtures\Fixtures26;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Embeddable;

/**
 * @Embeddable(columnPrefix="address_")
 */
#[Embeddable(columnPrefix: 'address_')]
class Address
{
    /** @Column(type="string") */
    #[Column(type: 'string')]
    protected $city;

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
