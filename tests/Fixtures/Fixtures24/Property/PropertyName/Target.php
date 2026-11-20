<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Fixtures\Fixtures24\Property\PropertyName;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;

/**
 * @Entity(table="to")
 */
#[Entity(table: 'to')]
class Target
{
    /**
     * @Column(type="primary")
     */
    #[Column(type: 'primary')]
    public int $id;

    /**
     * @Column(type="integer", name="outer_key")
     */
    #[Column(type: 'integer', name: 'outer_key')]
    public int $outerKey;
}
