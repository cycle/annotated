<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Fixtures\Fixtures1;

use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Column;

/** * @Entity(table="WithColumnInEntity", columns={@Column(name="columnDeclaredInEntity", type="integer")}) */
class WithColumnInEntity implements LabelledInterface
{
    /** @Column(type="primary") */
    public $id;

    public $columnDeclaredInEntity = 123;
}