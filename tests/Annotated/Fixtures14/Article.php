<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Fixtures14;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Relation\BelongsTo;
use Cycle\Annotated\Annotation\Relation\RefersTo;

/**
 * @Entity()
 */
#[Entity]
class Article extends Post
{
    /** @Column(type="string") */
    #[Column(type: 'string')]
    public $title;

    /** @BelongsTo(target="Some", innerKey={"id1", "id2"}, outerKey={"id1", "id2"}) */
    #[BelongsTo(target: Some::class, innerKey: ['id1', 'id2'], outerKey: ['id1', 'id2'])]
    protected ?Some $some;

    /** @RefersTo(target="Some", innerKey={"id1", "id2"}, outerKey={"id1", "id2"}) */
    #[RefersTo(target: Some::class, innerKey: ['id1', 'id2'], outerKey: ['id1', 'id2'])]
    protected ?Some $bestSome;
}
