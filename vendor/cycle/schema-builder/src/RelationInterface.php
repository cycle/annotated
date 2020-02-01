<?php

/**
 * Cycle ORM Schema Builder.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

declare(strict_types=1);

namespace Cycle\Schema;

use Cycle\Schema\Exception\RelationException;
use Cycle\Schema\Relation\OptionSchema;
use Spiral\Database\Exception\DBALException;

/**
 * Carries information about particular relation and table declaration required to properly
 * map two or more entities.
 */
interface RelationInterface
{
    /**
     * Create relation version linked to specific entity context.
     *
     * @param string       $name
     * @param string       $source
     * @param string       $target
     * @param OptionSchema $options
     * @return RelationInterface
     *
     * @throws RelationException
     */
    public function withContext(
        string $name,
        string $source,
        string $target,
        OptionSchema $options
    ): RelationInterface;

    /**
     * Compute relation references (column names and etc). Also ensures existence of fields in every
     * related object.
     *
     * @param Registry $registry
     *
     * @throws RelationException
     */
    public function compute(Registry $registry);

    /**
     * Render needed relation indexes and foreign keys into table.
     *
     * @param Registry $registry
     *
     * @throws RelationException
     * @throws DBALException
     */
    public function render(Registry $registry);

    /**
     * @return array
     */
    public function packSchema(): array;
}
