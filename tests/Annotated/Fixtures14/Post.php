<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Fixtures14;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;

/**
 * @Entity()
 */
#[Entity]
class Post
{
    /** @Column(type="primary") */
    #[Column(type: 'primary')]
    protected int $id1;

    /** @Column(type="primary") */
    #[Column(type: 'primary')]
    protected int $id2;
}
