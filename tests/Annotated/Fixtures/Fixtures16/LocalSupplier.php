<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Fixtures\Fixtures16;

use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Inheritance\JoinedTable as InheritanceJoinedTable;

/**
 * @Entity(table="local_suppliers")
 * @InheritanceJoinedTable(outerKey="index_id")
 */
#[Entity(
    table: 'local_suppliers'
)]
#[InheritanceJoinedTable(outerKey: 'index_id')]
class LocalSupplier extends Supplier
{
}
