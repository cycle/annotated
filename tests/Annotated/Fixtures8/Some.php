<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Fixtures8;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Relation\HasOne;

/**
 * @Entity()
 */
#[Entity]
class Some
{
    /** @Column(type="primary") */
    #[Column(type: 'primary')]
    protected $id;

    /** @HasOne(target="Article") */
    #[HasOne(target: 'Article')]
    protected $article;
}
