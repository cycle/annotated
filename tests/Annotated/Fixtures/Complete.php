<?php declare(strict_types=1);
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Cycle\Annotated\Tests\Fixtures;

/**
 * @entity(
 *      role       = eComplete,
 *      mapper     = CompleteMapper,
 *      repository = "Repository/CompleteRepository",
 *      source     = Source\TestSource,
 *      constrain  = Constrain\SomeConstrain,
 *      database   = "secondary",
 *      table      = "complete_data"
 * )
 */
class Complete implements LabelledInterface
{
    /**
     * @column(type=primary)
     * @var int
     */
    protected $id;

    /**
     * @column(type=string, name=username)
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $ignored;

    /**
     * @belongsTo(target=Simple)
     * @var Simple
     */
    protected $parent;
}