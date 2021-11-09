<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Fixtures15;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Relation\ManyToMany;

/**
 * @Entity()
 */
#[Entity]
class Post
{
    /** @Column(type="integer", primary=true) */
    #[Column(type: 'integer', primary: true)]
    protected int $id1;

    /** @Column(type="integer", primary=true) */
    #[Column(type: 'integer', primary: true)]
    protected int $id2;

    /** @ManyToMany(
     *     target="Tag",
     *     innerKey={"id1", "id2"},
     *     outerKey={"id1", "id2"},
     *     throughInnerKey={"id1", "id2"},
     *     throughOuterKey={"id3", "id4"},
     *     through="Context",
     *     indexCreate=false
     * )
     */
    #[ManyToMany(
        target: Tag::class,
        innerKey: ['id1', 'id2'],
        outerKey: ['id1', 'id2'],
        throughInnerKey: ['id1', 'id2'],
        throughOuterKey: ['id3', 'id4'],
        through: Context::class,
        indexCreate: false
    )]
    protected array $tags = [];
}
