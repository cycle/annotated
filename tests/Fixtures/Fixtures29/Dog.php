<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Fixtures\Fixtures29;

use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Inheritance\SingleTable;

#[Entity(role: 'dog')]
#[SingleTable]
class Dog extends Animal {}
