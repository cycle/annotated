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
class Label
{
    /**
     * @column(type=primary)
     * @var int
     */
    protected $id;

    /**
     * @column(type=text)
     * @var string
     */
    protected $text;

    /**
     * @belongsToMorphed(target=LabelledInterface)
     * @var LabelledInterface|null
     */
    protected $owner;
}