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
use Cycle\Annotated\Annotation\Relation\Morphed\BelongsToMorphed;

/**
 * @Entity()
 */
class Label
{
    /** @Column(type="primary") */
    protected $id;

    /** @Column(type="text") */
    protected $text;

    /** @BelongsToMorphed(target="LabelledInterface", indexCreate=false) */
    protected $owner;
}
