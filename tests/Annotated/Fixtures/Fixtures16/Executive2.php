<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Fixtures\Fixtures16;

use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Inheritance\JoinedTable as InheritanceJoinedTable;

/**
 * @Entity
 * @InheritanceJoinedTable(outerKey="foo_id")
 */
#[Entity]
#[InheritanceJoinedTable(outerKey: 'foo_id')]
class Executive2 extends Executive {}
