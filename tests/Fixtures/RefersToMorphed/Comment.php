<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Fixtures\RefersToMorphed;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Relation\Morphed\RefersToMorphed;

/**
 * @Entity
 */
#[Entity]
class Comment
{
    /** @Column(type="primary") */
    #[Column(type: 'primary')]
    protected $id;

    /** @Column(type="string") */
    #[Column(type: 'string')]
    protected $message;

    /** @RefersToMorphed(target="MorphedParentInterface") */
    #[RefersToMorphed(target: 'MorphedParentInterface')]
    protected $parent;
}
