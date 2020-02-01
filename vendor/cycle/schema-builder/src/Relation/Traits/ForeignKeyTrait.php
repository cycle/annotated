<?php

/**
 * Cycle ORM Schema Builder.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

declare(strict_types=1);

namespace Cycle\Schema\Relation\Traits;

use Cycle\Schema\Definition\Entity;
use Cycle\Schema\Definition\Field;
use Cycle\Schema\Registry;
use Cycle\Schema\Relation\OptionSchema;
use Cycle\Schema\Relation\RelationSchema;

trait ForeignKeyTrait
{
    /**
     * Create foreign key between two entities. Only when both entities are located
     * in a same database.
     *
     * @param Registry $registry
     * @param Entity   $source
     * @param Entity   $target
     * @param Field    $innerField
     * @param Field    $outerField
     */
    protected function createForeignKey(
        Registry $registry,
        Entity $source,
        Entity $target,
        Field $innerField,
        Field $outerField
    ): void {
        if ($registry->getDatabase($source) !== $registry->getDatabase($target)) {
            return;
        }

        $registry->getTableSchema($target)
                 ->foreignKey([$outerField->getColumn()])
                 ->references($registry->getTable($source), [$innerField->getColumn()])
                 ->onUpdate($this->getOptions()->get(RelationSchema::FK_ACTION))
                 ->onDelete($this->getOptions()->get(RelationSchema::FK_ACTION));
    }

    /**
     * @return OptionSchema
     */
    abstract protected function getOptions(): OptionSchema;
}
