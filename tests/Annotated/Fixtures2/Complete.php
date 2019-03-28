<?php declare(strict_types=1);
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Cycle\Annotated\Tests\Fixtures2;

/**
 * @entity(role = eComplete)
 */
class Complete
{
    /**
     * @column(type=primary)
     * @var int
     */
    protected $id;

    /**
     * @belongsTo(target=Simple,inverse=@inverse(type=hasOne,as="child"))
     * @var Simple
     */
    protected $parent;

    /**
     * @belongsTo(target=Simple,innerKey=uncle_id,inverse=@inverse(type=hasMany,as="stepKids"))
     * @var Simple
     */
    protected $uncles;
}