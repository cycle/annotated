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

    #[Column(type: 'tinyInteger', unsigned: true)]
    private int $unsigned = 1;

    #[Column(type: 'tinyInteger', zerofill: true)]
    private int $zerofill = 1;

    #[Column(type: 'tinyInteger')]
    private int $simple = 1;
}
