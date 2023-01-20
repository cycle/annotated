<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Fixtures\Fixtures22\Jti;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;

/**
 * @Entity
 */
#[Entity(table: 'big_primary')]
class Person
{
    /** @Column(type="bigInteger", primary=true) */
    #[Column(type: 'bigInteger', primary: true)]
    protected int $id;
}
