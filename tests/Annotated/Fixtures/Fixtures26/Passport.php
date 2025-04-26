<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Fixtures\Fixtures26;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Obsolete;
use Cycle\Annotated\Annotation\Relation\BelongsTo;

/**
 * @Entity(table="passport")
 */
#[Entity(table: 'passport')]
class Passport
{
    /** @Column(type="primary") */
    #[Column(type: 'primary')]
    protected $id;

    /** @Column(type="string") */
    #[Column(type: 'string')]
    protected $number;

    /**
     * @Obsolete
     * @BelongsTo(target=User::class)
     */
    #[Obsolete]
    #[BelongsTo(target: User::class, innerKey: 'passport_id', outerKey: 'id')]
    protected User $user;
}
