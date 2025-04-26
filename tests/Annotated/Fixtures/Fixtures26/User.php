<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Fixtures\Fixtures26;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Obsolete;

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
