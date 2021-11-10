<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Fixtures;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Relation\Morphed\MorphedHasOne;

/**
 * @Entity(
 *     typecast={
 *      Typecast\Typecaster::class,
 *      "Cycle\Annotated\Tests\Fixtures\Typecast\UuidTypecaster",
 *      "foo"
 *     }
 * )
 */
#[Entity(typecast: [Typecast\Typecaster::class, Typecast\UuidTypecaster::class, 'foo'])]
class Tag
{
    /** @Column(type="primary") */
    #[Column(type: 'primary')]
    protected $id;

    /** @MorphedHasOne(target="Label", outerKey="owner_id", morphKey="owner_role", indexCreate=false) */
    #[MorphedHasOne(target: 'Label', outerKey: 'owner_id', morphKey: 'owner_role', indexCreate: false)]
    protected $label;
}
