<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Fixtures\Fixtures24\Class\PropertyName;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\ForeignKey;

/**
 * @Entity(role="from", table="from")
 * @ForeignKey(target="target", outerKey="outerKey", innerKey="innerKey")
 */
#[ForeignKey(target: Target::class, outerKey: 'outerKey', innerKey: 'innerKey')]
#[Entity(role: 'from', table: 'from')]
class PropertyName
{
    /**
     * @Column(type="primary")
     */
    #[Column(type: 'primary')]
    public int $id;

    /**
     * @Column(type="integer", name="inner_key")
     */
    #[Column(type: 'integer', name: 'inner_key')]
    public int $innerKey;
}
