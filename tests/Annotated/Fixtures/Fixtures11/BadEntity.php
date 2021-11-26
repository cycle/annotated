<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Fixtures\Fixtures11;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Table;

/**
 * @Entity()
 * @Table(
 *      columns = {
 *          "name1": @Column(property="name2", type="string")
 *      }
 * )
 */
class BadEntity
{
    /** @Column(type="primary") */
    protected $id;
}
