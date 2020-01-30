<?php

/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Fixtures9;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Table;
use Cycle\Annotated\Annotation\Table\Index;

/**
 * @Entity()
 * @Table(
 *      columns = {
 *          "name": @Column(type="string"),
 *      },
 *      indexes = {
 *          @Index(columns={"name", "id DESC"}),
 *      }
 * )
 */
class WithTableOrderedIndex
{
    /** @Column(type="primary") */
    protected $id;
}
