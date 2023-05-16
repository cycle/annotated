<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Fixtures\Fixtures23\STIWithEntityColumn;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Inheritance\DiscriminatorColumn;

/**
 * @Entity(columns={
 *     @Column(type="primary", property="id"),
 *     @Column(type="string", property="action")
 * })
 * @DiscriminatorColumn(name="action")
 */
abstract class BaseEvent
{
    public int $id;
    public string $action;
}
