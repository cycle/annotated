<?php

/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Fixtures2;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Relation\HasOne;
use Cycle\Annotated\Annotation\Relation\Inverse;

/**
 * @Entity()
 */
class User implements MarkedInterface
{
    /** @Column(type="primary") */
    protected $id;

    /** @HasOne(target="Simple", inverse=@Inverse(as="user", type="belongsTo")) */
    protected $simple;
}
