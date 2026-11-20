<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Fixtures\Fixtures29;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Relation\HasOne;

#[Entity(role: 'dogOwner')]
class DogOwner
{
    #[Column(type: 'primary')]
    public int $id;

    #[HasOne(target: Dog::class)]
    public Dog $dog;
}
