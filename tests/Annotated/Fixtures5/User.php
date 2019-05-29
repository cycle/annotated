<?php declare(strict_types=1);
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Cycle\Annotated\Tests\Fixtures5;

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
     * @hasOne(target=Simple, inverse=(as="user", type=belongsTo, load=lazy), load=eager)
     * @var Simple
     */
    protected $simple;
}