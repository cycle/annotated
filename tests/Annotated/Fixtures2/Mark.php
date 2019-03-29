<?php declare(strict_types=1);
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Cycle\Annotated\Tests\Fixtures2;

/**
 * @entity
 */
class Mark
{
    /**
     * @column(type=primary)
     * @var int
     */
    protected $id;

    /**
     * @belongsToMorphed(target=MarkedInterface, inverse=@inverse(as="mark",type=morphedHasOne))
     * @var MarkedInterface
     */
    protected $owner;
}