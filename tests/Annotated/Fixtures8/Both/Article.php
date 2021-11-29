<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Fixtures8\Both;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;

/**
 * @Entity()
 */
#[Entity]
class Article extends Post
{
    /** @Column(type="string") */
    #[Column(type: 'string')]
    public $title;
}
