<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Fixtures\Fixtures1;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;

/**
 * @Entity()
 */
#[Entity]
class Child extends Simple
{
    /** @Column(type="string") */
    #[Column(type: 'string')]
    protected $name;
}
