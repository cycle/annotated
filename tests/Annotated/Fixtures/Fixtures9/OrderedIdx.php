<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Fixtures\Fixtures9;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Table;
use Cycle\Annotated\Annotation\Table\Index;

/**
 * @Entity()
 * @Table(
 *      columns = {
 *          "name": @Column(type="string"),
 *          @Column(property="other_name", type="string"),
 *      },
 *      indexes = {
 *          @Index(columns={"name", "id DESC"}),
 *      }
 * )
 */
#[Entity]
#[Table(
    columns: [
        'name' => new Column(type: 'string'),
        new Column(type: 'string', property: 'other_name'),
    ],
    indexes: [
        new Index(columns: ['name', 'id DESC']),
    ],
)]
class OrderedIdx
{
    /** @Column(type="primary") */
    #[Column(type: 'primary')]
    protected $id;
}
