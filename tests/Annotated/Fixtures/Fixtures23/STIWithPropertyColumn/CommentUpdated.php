<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Fixtures\Fixtures23\STIWithPropertyColumn;

use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Inheritance\SingleTable;
use Cycle\Annotated\Annotation\Relation\Morphed\BelongsToMorphed;

/**
 * @Entity
 * @SingleTable(value="comment.updated")
 */
#[Entity]
#[SingleTable(value: 'comment.updated')]
final class CommentUpdated extends BaseEvent
{
    /**
     * @BelongsToMorphed(
     *     target="EventEmitterInterface",
     *     innerKey="object_id",
     *     morphKey="object_type",
     *     indexCreate=false
     * )
     */
    #[BelongsToMorphed(
        target: EventEmitterInterface::class,
        innerKey: 'object_id',
        morphKey: 'object_type',
        indexCreate: false,
    )]
    public EventEmitterInterface|Comment $object;
}
