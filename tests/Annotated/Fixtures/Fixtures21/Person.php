<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Fixtures\Fixtures21;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Inheritance\DiscriminatorColumn;

/**
 * @Entity
 * @DiscriminatorColumn(name="type")
 */
#[Entity]
#[DiscriminatorColumn(name: 'type')]
class Person
{
    /** @Column(type="bigInteger", primary="true") */
    #[Column(type: 'bigInteger', primary: true)]
    protected int $id;

    /** @Column(type="string") */
    #[Column(type: 'string')]
    public string $name;

    /** @Column(type="string") */
    #[Column(type: 'string')]
    public string $type;
}
