<?php declare(strict_types=1);
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Cycle\Annotated\Tests\Fixtures5;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Relation\HasOne;
use Cycle\Annotated\Annotation\Relation\Inverse;

/**
 * @Entity()
 */
class User
{
    /** @Column(type="primary") */
    protected $id;

    /** @HasOne(target=Simple::class, inverse=@Inverse(as="user", type="belongsTo", load="lazy"), load="eager") */
    protected $simple;
}
