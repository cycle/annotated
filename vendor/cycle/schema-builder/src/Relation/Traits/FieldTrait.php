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
use Cycle\Schema\Exception\FieldException;
use Cycle\Schema\Exception\RelationException;
use Cycle\Schema\Relation\OptionSchema;
use Cycle\Schema\Table\Column;

trait FieldTrait
{
    /**
     * @param Entity $entity
     * @param int $field
     * @return Field
     */
    protected function getField(Entity $entity, int $field): Field
    {
        try {
            return $entity->getFields()->get($this->getOptions()->get($field));
        } catch (FieldException $e) {
            throw new RelationException(
                sprintf(
                    'Field `%s`.`%s` does not exists, referenced by `%s`',
                    $entity->getRole(),
                    $this->getOptions()->get($field),
                    $this->source
                ),
                $e->getCode(),
                $e
            );
        }
    }

    /**
     * @param Entity $target
     * @param string $name
     * @param Field $outer
     * @param bool $nullable
     */
    protected function ensureField(Entity $target, string $name, Field $outer, bool $nullable = false): void
    {
        // ensure that field will be indexed in memory for fast references
        $outer->setReferenced(true);

        if ($target->getFields()->has($name)) {
            // field already exists and defined by the user
            return;
        }

        $field = new Field();
        $field->setColumn($name);
        $field->setTypecast($outer->getTypecast());

        switch ($outer->getType()) {
            case 'primary':
                $field->setType('int');
                break;
            case 'bigPrimary':
                $field->setType('bigint');
                break;
            default:
                $field->setType($outer->getType());
        }

        if ($nullable) {
            $field->getOptions()->set(Column::OPT_NULLABLE, true);
        }

        $target->getFields()->set($name, $field);
    }

    /**
     * @return OptionSchema
     */
    abstract protected function getOptions(): OptionSchema;
}
