<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Fixtures\Fixtures2;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Relation\Inverse;
use Cycle\Annotated\Annotation\Relation\Morphed\BelongsToMorphed;

/**
 * @Entity()
 */
#[Entity]
class Mark
{
    /** @Column(type="primary") */
    #[Column(type: 'primary')]
    protected $id;

    /** @BelongsToMorphed(target="MarkedInterface", inverse=@Inverse(as="mark", type="morphedHasOne")) */
    #[BelongsToMorphed(target: 'MarkedInterface')]
    #[Inverse(as: 'mark', type: 'morphedHasOne')]
    protected $owner;
}
