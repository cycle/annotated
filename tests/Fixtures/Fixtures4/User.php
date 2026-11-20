<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Fixtures\Fixtures4;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;

/**
 * @Entity()
 */
#[Entity]
class User
{
    /** @Column */
    #[Column]
    protected $id;
}
