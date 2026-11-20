<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Fixtures\TableInheritance;

use Cycle\Annotated\Annotation\Entity;

#[Entity(role: 'bird', table: 'birds')]
#[Hybrid]
class Bird extends Animal {}
