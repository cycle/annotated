<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Fixtures\Fixtures16;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Inheritance\DiscriminatorColumn;

/**
 * @Entity
 * @DiscriminatorColumn(name="type")
 */
#[Entity]
#[DiscriminatorColumn(name: 'type')]
class Tool
{
    /** @Column(type="primary", name="id") */
    #[Column(type: 'primary', name: 'id')]
    public int $tool_id;
}
