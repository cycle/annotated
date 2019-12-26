<?php

/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Fixtures;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Relation\Morphed\MorphedHasOne;

/**
 * @Entity()
 */
class Tag
{
    /** @Column(type="primary") */
    protected $id;

    /** @MorphedHasOne(target="Label", outerKey="owner_id", morphKey="owner_role", indexCreate=false) */
    protected $label;
}
