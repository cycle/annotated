<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Fixtures\Fixtures26;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Relation\HasMany;

/**
 * @Entity(table="city")
 */
#[Entity(table: 'city')]
class City
{
    /** @Column(type="primary") */
    #[Column(type: 'primary')]
    public $id;

    /** @Column(type="string") */
    #[Column(type: 'string')]
    public $name;

    /** @HasMany(target=User::class, fkCreate=false, obsolete=true) */
    #[HasMany(target: User::class, fkCreate: false, obsolete: true)]
    private array $bornUsers = [];
}
