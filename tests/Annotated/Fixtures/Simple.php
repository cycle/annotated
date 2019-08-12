<?php declare(strict_types=1);
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Cycle\Annotated\Tests\Fixtures;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Doctrine\Common\Collections\Collection;

/**
 * @Entity(role="simple")
 */
class Simple
{
    /**
     * @Column(type=primary)
     * @var int
     */
    protected $id;

    /**
     * @hasOne(target=Complete)
     * @var Complete
     */
    protected $one;

    /**
     * @hasMany(target=WithTable)
     * @var WithTable[]|Collection
     */
    protected $many;

    /**
     * @refersTo(target=Simple, fkAction="NO ACTION")
     */
    protected $parent;

    /**
     * @morphedHasMany(target=Label,outerKey=owner_id,morphKey=owner_role)
     */
    protected $labels;
}