<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Fixtures\Fixtures23\STIWithTableColumn;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Table;

/**
 * @Entity
 * @Table(columns={
 *    @Column(type="primary", property="id"),
 *    @Column(type="string", property="body", nullable=true)
 * })
 */
#[Entity]
#[Table(columns: [
    new Column(type: 'primary', property: 'id'),
    new Column(type: 'string', property: 'body', nullable: true)
])]
final class Comment implements EventEmitterInterface
{
    public int $id;
    public ?string $body = null;
}
