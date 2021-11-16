<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Fixtures16;

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
    /** @Column(type="primary") */
    #[Column(type: 'primary', primary: true)]
    protected int $id;

    /** @Column(type="string") */
    #[Column(type: 'string')]
    protected string $name;
}
