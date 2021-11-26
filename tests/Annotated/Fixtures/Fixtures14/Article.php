<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Fixtures\Fixtures14;

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
    /** @Column(type="integer", nullable=true) */
    #[Column(type: 'integer', nullable: true)]
    protected int $field1;

    /** @Column(type="integer", nullable=true) */
    #[Column(type: 'integer', nullable: true)]
    protected int $field2;

    /** @Column(type="string") */
    #[Column(type: 'string')]
    public $title;

    /** @BelongsTo(target="Some", innerKey={"id1", "id2"}, outerKey={"id1", "id2"}, fkAction="NO ACTION") */
    #[BelongsTo(target: Some::class, innerKey: ['id1', 'id2'], outerKey: ['id1', 'id2'], fkAction: 'NO ACTION')]
    protected ?Some $some;

    /** @RefersTo(target="Some", innerKey={"id1", "id2"}, outerKey={"id1", "id2"}, fkAction="NO ACTION") */
    #[RefersTo(target: Some::class, innerKey: ['field1', 'field2'], outerKey: ['id1', 'id2'], fkAction: 'NO ACTION')]
    protected ?Some $bestSome;
}
