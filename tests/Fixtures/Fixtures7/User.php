<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Fixtures\Fixtures7;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
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
}
