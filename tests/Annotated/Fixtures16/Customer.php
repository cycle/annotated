<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Fixtures16;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Inheritance\SingleTable as InheritanceSingleTable;

/**
 * @Entity
 * @InheritanceSingleTable(value="foo_customer")
 */
#[Entity]
#[InheritanceSingleTable(value: 'foo_customer')]
class Customer extends Person
{
    /** @Column(type="json") */
    #[Column(type: 'json')]
    protected array $preferences;
}
