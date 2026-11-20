<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Fixtures\Fixtures2;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Relation\BelongsTo;
use Cycle\Annotated\Annotation\Relation\Inverse;

/**
 * @Entity(role = "eComplete")
 */
#[Entity(role: 'eComplete')]
class Complete
{
    /** @Column(type="primary") */
    #[Column(type: 'primary')]
    protected $id;

    /** @BelongsTo(target="Simple", fkAction="NO ACTION", inverse=@Inverse(as="child", type="hasOne")) */
    #[BelongsTo(target: 'Simple', fkAction: 'NO ACTION')]
    #[Inverse(as: 'child', type: 'hasOne')]
    protected $parent;

    /** @belongsTo(
     *     target="Simple",
     *     fkAction="NO ACTION",
     *     innerKey="uncle_id",
     *     inverse=@Inverse(as="stepKids", type="hasMany")
     * )
     */
    #[BelongsTo(target: 'Simple', fkAction: 'NO ACTION', innerKey: 'uncle_id')]
    #[Inverse(as: 'stepKids', type: 'hasMany')]
    protected $uncles;
}
