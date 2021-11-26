<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Fixtures\Fixtures1\Tag;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;

/**
 * @Entity(role="tagContext")
 */
#[Entity(role: 'tagContext')]
class Context
{
    /** @Column(type="primary") */
    #[Column(type: 'primary')]
    protected $id;
}
