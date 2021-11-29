<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Fixtures8\Annotation;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;

/**
 * @Entity()
 */
class Article extends Post
{
    /** @Column(type="string") */
    public $title;
}
