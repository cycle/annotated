<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */
declare(strict_types=1);

namespace Cycle\Annotated\Tests\Fixtures6;

/**
 * @embeddable(role=address, columnPrefix="address_")
 * @table(indexes={@index(columns={zipcode})})
 */
class Address
{
    /**
     * @column(type=string)
     */
    protected $city;

    /**
     * @column(type=string)
     */
    protected $country;

    /**
     * @column(type=string)
     */
    protected $address;

    /**
     * @column(type=int)
     */
    protected $zipcode;
}