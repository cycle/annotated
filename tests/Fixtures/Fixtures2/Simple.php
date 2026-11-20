<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Fixtures\Fixtures2;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;

/**
 * @Entity()
 */
#[Entity]
class Simple implements MarkedInterface
{
    /** @Column(type="primary") */
    #[Column(type: 'primary')]
    protected $id;
}
