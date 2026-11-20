<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Fixtures\Fixtures20;

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
    /** @Column(type="string") */
    #[Column(type: 'string')]
    public string $name;

    /** @Column(type="string") */
    #[Column(type: 'string')]
    public string $type;

    /** @Column(type="primary", name="id") */
    #[Column(type: 'primary', name: 'id')]
    protected int $foo_id;
}
