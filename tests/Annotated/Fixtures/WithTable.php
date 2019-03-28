<?php declare(strict_types=1);
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Cycle\Annotated\Tests\Fixtures;

/**
 * @entity
 * @table(
 *      columns={name: @column(type=string), status: (type="enum(active,disabled)", default=active)},
 *      indexes={@index(columns={status}), (columns={name}, unique=true, name=name_index)}
 * )
 */
class WithTable
{
    /**
     * @column(type=primary)
     * @var int
     */
    protected $id;
}