<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Fixtures\Fixtures7;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Obsolete;
use Cycle\Annotated\Annotation\Relation\Embedded;

/**
 * @Entity()
 */
#[Entity]
class User
{
    /** @Column(type="primary") */
    #[Column(type: 'primary')]
    protected $id;

    /** @Embedded(target="Address", load="lazy") */
    #[Embedded(target: 'Address', load: 'lazy')]
    protected $address;

    /**
     * @Column(type="integer", nullable=true)
     * @Obsolete
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
    private $secret;
}
