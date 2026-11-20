<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Fixtures\TableInheritance;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Inheritance\JoinedTable;

#[Entity(role: 'dog', table: 'dogs')]
#[JoinedTable]
class Dog extends Animal
{
    #[Column(type: 'string')]
    public string $breed;
}
