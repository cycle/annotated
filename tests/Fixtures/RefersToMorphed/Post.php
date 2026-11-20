<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Fixtures\RefersToMorphed;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;

/**
 * @Entity
 */
#[Entity]
class Post implements MorphedParentInterface
{
    /** @Column(type="primary") */
    #[Column(type: 'primary')]
    protected $id;

    /** @Column(type="string") */
    #[Column(type: 'string')]
    protected $title;
}
