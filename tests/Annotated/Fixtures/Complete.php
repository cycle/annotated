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
use Cycle\Annotated\Annotation\Relation\BelongsTo;

/**
 * @Entity(
 *      role       = "eComplete",
 *      mapper     = "CompleteMapper",
 *      repository = "Repository/CompleteRepository",
 *      source     = "Source\TestSource",
 *      constrain  = "Constrain\SomeConstrain",
 *      database   = "secondary",
 *      table      = "complete_data"
 * )
 */
class Complete implements LabelledInterface
{
    /** @Column(type="primary") */
    protected $id;

    /** @Column(type="string", name="username") */
    protected $name;

    /** @var string */
    protected $ignored;

    /** @BelongsTo(target="Simple") */
    protected $parent;
}