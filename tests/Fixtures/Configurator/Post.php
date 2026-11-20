<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Fixtures\Configurator;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\ForeignKey;
use Cycle\Annotated\Annotation\Relation\HasMany;
use Cycle\Annotated\Annotation\Relation\Inverse;

#[Entity(typecast: ['Typecaster', 'Sub/Typecaster'])]
class Post
{
    #[Column(type: 'primary')]
    public int $id;

    #[Column(type: 'integer')]
    #[ForeignKey(target: Author::class, innerKey: 'author_ref', outerKey: 'id')]
    public int $authorId;

    #[HasMany(target: 'Comment', collection: 'Sub/Comments', inverse: new Inverse(as: 'post', type: 'belongsTo'))]
    #[Inverse(as: 'article', type: 'belongsTo')]
    public array $comments;
}
