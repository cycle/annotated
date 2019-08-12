<?php declare(strict_types=1);
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Cycle\Annotated\Tests\Fixtures7;

use Cycle\Annotated\Annotation\Relation\Embedded;

/**
 * @entity
 */
class User
{
    /**
     * @column(type=primary)
     * @var int
     */
    protected $id;

    /**
     * @Embedded(target="Address", load="lazy")
     * @var Address
     */
    protected $address;
}