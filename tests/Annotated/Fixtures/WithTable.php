<?php declare(strict_types=1);
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Cycle\Annotated\Tests\Fixtures;

use Cycle\ORM\Relation\Pivoted\PivotedCollectionInterface;

/**
 * @entity
 * @table(
 *      columns={name: @column(type=string), status: (type="enum(active,disabled)", default=active)},
 *      indexes={@index(columns={status}), (columns={name}, unique=true, name=name_index)}
 * )
 */
class WithTable implements LabelledInterface
{
    /**
     * @column(type=primary)
     * @var int
     */
    protected $id;

    /**
     * @manyToMany(target=Tag, though="Tag/Context")
     * @var PivotedCollectionInterface|Tag[]
     */
    protected $tags;

    /**
     * @morphedHasMany(target=Label,outerKey=owner_id,morphKey=owner_role)
     */
    protected $labels;
}