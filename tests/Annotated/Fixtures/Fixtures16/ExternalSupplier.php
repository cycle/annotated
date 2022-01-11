<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Fixtures\Fixtures16;

use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Inheritance\JoinedTable as InheritanceJoinedTable;

/**
 * @Entity(table="external_suppliers")
 * @InheritanceJoinedTable(outerKey="custom_id")
 */
#[Entity(table: 'external_suppliers')]
#[InheritanceJoinedTable(outerKey: 'custom_id')]
class ExternalSupplier extends Supplier
{
}
