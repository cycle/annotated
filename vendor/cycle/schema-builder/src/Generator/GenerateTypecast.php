<?php

/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

declare(strict_types=1);

namespace Cycle\Schema\Generator;

use Cycle\Schema\Definition\Entity;
use Cycle\Schema\GeneratorInterface;
use Cycle\Schema\Registry;
use Spiral\Database\Schema\AbstractColumn;

/**
 * Must be run after RenderTable.
 */
final class GenerateTypecast implements GeneratorInterface
{
    /**
     * @param Registry $registry
     * @return Registry
     */
    public function run(Registry $registry): Registry
    {
        foreach ($registry as $entity) {
            $this->compute($registry, $entity);
        }

        return $registry;
    }

    /**
     * Automatically clarify column types based on table column types.
     *
     * @param Registry $registry
     * @param Entity   $entity
     */
    protected function compute(Registry $registry, Entity $entity): void
    {
        if (!$registry->hasTable($entity)) {
            return;
        }

        $table = $registry->getTableSchema($entity);

        foreach ($entity->getFields() as $field) {
            if ($field->hasTypecast() || !$table->hasColumn($field->getColumn())) {
                continue;
            }

            $column = $table->column($field->getColumn());

            $field->setTypecast($this->typecast($column));
        }
    }

    /**
     * @param AbstractColumn $column
     * @return string|callable
     */
    private function typecast(AbstractColumn $column)
    {
        switch ($column->getType()) {
            case AbstractColumn::BOOL:
                return 'bool';
            case AbstractColumn::INT:
                return 'int';
            case AbstractColumn::FLOAT:
                return 'float';
        }

        if (in_array($column->getAbstractType(), ['datetime', 'date', 'time', 'timestamp'])) {
            return 'datetime';
        }

        return null;
    }
}
