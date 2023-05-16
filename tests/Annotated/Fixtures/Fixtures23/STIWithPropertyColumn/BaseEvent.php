<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Fixtures\Fixtures23\STIWithPropertyColumn;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Inheritance\DiscriminatorColumn;

/**
 * @Entity
 * @DiscriminatorColumn(name="action")
 */
#[Entity]
#[DiscriminatorColumn(name: 'action')]
abstract class BaseEvent
{
    /**
     * @Column(type="primary")
     */
    #[Column(type: 'primary')]
    public int $id;

    /**
     * @Column(type="string")
     */
    #[Column(type: 'string')]
    public string $action;
}
