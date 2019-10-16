<?php

declare(strict_types=1);

/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Cycle\Annotated\Tests\Fixtures7;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Relation\Embedded;

/**
 * @Entity()
 */
class User
{
    /** @Column(type="primary") */
    protected $id;

    /** @Embedded(target="Address", load="lazy") */
    protected $address;
}
