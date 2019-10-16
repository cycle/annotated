<?php

declare(strict_types=1);

/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Cycle\Annotated\Tests\Fixtures2;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Relation\Inverse;
use Cycle\Annotated\Annotation\Relation\Morphed\BelongsToMorphed;

/**
 * @Entity()
 */
class Mark
{
    /** @Column(type="primary") */
    protected $id;

    /** @BelongsToMorphed(target="MarkedInterface", inverse=@Inverse(as="mark", type="morphedHasOne")) */
    protected $owner;
}
