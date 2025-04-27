<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Fixtures\Fixtures26;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
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
     * @BelongsTo(target=User::class, obsolete=true)
     */
    #[BelongsTo(target: User::class, innerKey: 'passport_id', outerKey: 'id', obsolete: true)]
    protected User $user;
}
