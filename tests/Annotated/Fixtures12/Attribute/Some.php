<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Fixtures8\Annotation;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Relation\HasOne;

#[Entity]
class Some
{
    #[Column(type: 'primary')]
    protected $id;

    #[HasOne(target: 'Article')]
    protected $article;
}
