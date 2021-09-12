<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Fixtures14;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Relation\HasMany;
use Cycle\Annotated\Annotation\Relation\HasOne;
use Cycle\Annotated\Annotation\Relation\ManyToMany;

/**
 * @Entity()
 */
#[Entity]
class Some
{
    /** @Column(type="primary") */
    #[Column(type: 'primary')]
    protected int $id1;

    /** @Column(type="primary") */
    #[Column(type: 'primary')]
    protected int $id2;

    /** @HasOne(target="Article", innerKey={"id1", "id2"}, outerKey={"id1", "id2"}) */
    #[HasOne(target: Article::class, innerKey: ['id1', 'id2'], outerKey: ['id1', 'id2'])]
    protected ?Article $article;

    /** @HasMany(target="Article", innerKey={"id1", "id2"}, outerKey={"id1", "id2"}) */
    #[HasMany(target: Article::class, innerKey: ['id1', 'id2'], outerKey: ['id1', 'id2'])]
    protected array $articles = [];

    /** @ManyToMany(target="Post", innerKey={"id1", "id2"}, outerKey={"id1", "id2"}, throughInnerKey={"id1", "id2"}, throughOuterKey={"id3", "id4"}, through="Tag") */
    #[ManyToMany(target: Post::class, innerKey: ['id1', 'id2'], outerKey: ['id1', 'id2'], throughInnerKey: ['id1', 'id2'], throughOuterKey: ['id3', 'id4'], through: Tag::class)]
    protected array $posts = [];
}