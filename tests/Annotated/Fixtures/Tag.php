<?php declare(strict_types=1);
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Cycle\Annotated\Tests\Fixtures;

/**
 * @entity
 */
class Tag
{
    /**
     * @column(type=primary)
     * @var int
     */
    protected $id;

    /**
     * @morphedHasOne(target=Label,outerKey=owner_id,morphKey=owner_role,indexCreate=false)
     */
    protected $label;
}