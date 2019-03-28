<?php declare(strict_types=1);
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Cycle\Annotated\Tests\Fixtures;

use Doctrine\Common\Collections\Collection;

/**
 * @entity
 */
class Simple
{
    /**
     * @column(type=primary)
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