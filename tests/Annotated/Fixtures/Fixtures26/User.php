<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Fixtures\Fixtures26;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Obsolete;
use Cycle\Annotated\Annotation\Relation\Embedded;
use Cycle\Annotated\Annotation\Relation\HasOne;

/**
 * @Entity(table="user")
 */
#[Entity(table: 'user')]
class User
{
    /** @Column(type="primary") */
    #[Column(type: 'primary')]
    protected $id;

    /** @Column(type="string") */
    #[Column(type: 'string')]
    protected $name;

    /**
     * @Obsolete
     * @Column(type="integer", nullable=true)
     *
     * @deprecated Since May 5, 2025
     */
    #[Obsolete]
    #[Column(type: 'string', nullable: true)]
    protected $skype = null;

    /**
     * There is must not be problems with it.
     */
    #[Obsolete]
    protected $secret;

    /**
     * @Obsolete
     * @Embedded(target=Address::class)
     */
    #[Obsolete]
    #[Embedded(target: Address::class)]
    protected Address $address;

    /**
     * @Obsolete
     * @HasOne(target=Passport::class)
     */
    #[Obsolete]
    #[HasOne(target: Passport::class)]
    protected $passport;

    public function __construct()
    {
        $this->address = new Address();
    }
}
