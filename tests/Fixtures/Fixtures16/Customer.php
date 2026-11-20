<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Fixtures\Fixtures16;

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
    /** @Column(type="string") */
    #[Column(type: 'string')]
    public string $preferences;

    public function getPreferences(): string
    {
        return $this->preferences;
    }
}
