<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Fixtures\Fixtures1;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Relation\Morphed\BelongsToMorphed;

/**
 * @Entity(
 *      scope = "Constrain\SomeConstrain",
 * )
 */
#[Entity(scope: Constrain\SomeConstrain::class)]
class Label
{
    /** @Column(type="primary") */
    #[Column(type: 'primary')]
    protected $id;

    /** @Column(type="text") */
    #[Column(type: 'text')]
    protected $text;

    /** @BelongsToMorphed(target="LabelledInterface", indexCreate=false) */
    #[BelongsToMorphed(target: 'LabelledInterface', indexCreate: false)]
    protected $owner;
}
