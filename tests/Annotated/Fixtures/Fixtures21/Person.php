<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Fixtures\Fixtures21;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;

/**
 * @Entity
 */
#[Entity]
class Person
{
    /** @Column(type="primary") */
    #[Column(type: 'primary')]
    protected int $id;

    /** @Column(type="string") */
    #[Column(type: 'string')]
    public string $name;

    /** @Column(type="string") */
    #[Column(type: 'string')]
    public string $type;
}
