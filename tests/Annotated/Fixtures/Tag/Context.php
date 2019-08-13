<?php declare(strict_types=1);
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Cycle\Annotated\Tests\Fixtures\Tag;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;

/**
 * @Entity(role="tagContext")
 */
class Context
{
    /** @Column(type="primary") */
    protected $id;
}