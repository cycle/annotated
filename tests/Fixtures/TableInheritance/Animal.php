<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Fixtures\TableInheritance;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;

#[Entity(role: 'animal', table: 'animals')]
class Animal
{
    #[Column(type: 'bigPrimary')]
    public int $id;

    #[Column(type: 'string')]
    public string $name;
}
