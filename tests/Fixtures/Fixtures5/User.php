<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Fixtures\Fixtures5;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Relation\HasOne;
use Cycle\Annotated\Annotation\Relation\Inverse;

/**
 * @Entity()
 */
#[Entity]
class User
{
    /** @Column(type="primary") */
    #[Column(type: 'primary')]
    protected $id;

    /**
     * @HasOne(target=Simple::class, load="eager")
     * @Inverse(as="user", type="belongsTo", load="lazy")
     */
    #[HasOne(target: Simple::class, load: 'eager')]
    #[Inverse(as: 'user', type: 'belongsTo', load: 'lazy')]
    protected $simple;
}
