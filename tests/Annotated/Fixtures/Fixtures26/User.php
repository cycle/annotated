<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Fixtures\Fixtures26;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Relation\Embedded;
use Cycle\Annotated\Annotation\Relation\HasOne;
use Cycle\Annotated\Annotation\Relation\RefersTo;

/**
 * @Entity(table="user")
 */
#[Entity(table: 'user')]
class User
{
    /** @Column(type="primary") */
    #[Column(type: 'primary')]
    public $id;

    /**
     * @Column(type="integer", nullable=true, obsolete=true)
     *
     * @deprecated Since May 5, 2025
     */
    #[Column(type: 'string', nullable: true, obsolete:true)]
    public $skype = null;

    /**
     * @Embedded(target=Address::class, obsolete=true)
     */
    #[Embedded(target: Address::class, obsolete: true)]
    public Address $address;

    /**
     * @HasOne(target=Passport::class, obsolete=true)
     */
    #[HasOne(target: Passport::class, obsolete: true)]
    public Passport $passport;

    /** @RefersTo(target=City::class, innerKey="born_city_id", outerKey="id", obsolete=true) */
    #[RefersTo(target: City::class, innerKey: 'born_city_id', outerKey: 'id', obsolete: true)]
    public City $bornCity;

    public function __construct(City $bornCity)
    {
        $this->address = new Address();
        $this->bornCity = $bornCity;
    }
}
