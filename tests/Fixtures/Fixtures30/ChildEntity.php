<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Fixtures\Fixtures30;

use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Table\Index;

#[Entity]
#[Index(columns: ['child_column'])]
class ChildEntity extends ParentEntity {}
