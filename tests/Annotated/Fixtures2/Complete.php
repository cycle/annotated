<?php

/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Fixtures2;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Relation\BelongsTo;
use Cycle\Annotated\Annotation\Relation\Inverse;

/**
 * @Entity(role = "eComplete")
 */
class Complete
{
    /** @Column(type="primary") */
    protected $id;

    /** @BelongsTo(target="Simple", fkAction="NO ACTION", inverse=@Inverse(as="child", type="hasOne")) */
    protected $parent;

    /** @belongsTo(
     *     target="Simple",
     *     fkAction="NO ACTION",
     *     innerKey="uncle_id",
     *     inverse=@Inverse(as="stepKids", type="hasMany")
     * )
     */
    protected $uncles;
}
