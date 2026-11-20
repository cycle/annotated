<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Fixtures\TableInheritance;

use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Inheritance\JoinedTable;

#[Entity(role: 'cat', table: 'cats')]
#[JoinedTable(outerKey: 'missing')]
class Cat extends Animal {}
