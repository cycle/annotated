<?php

/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

declare(strict_types=1);

namespace Cycle\Annotated;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Embeddable;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Relation as RelationAnnotation;
use Cycle\Annotated\Exception\AnnotationException;
use Cycle\Schema\Definition\Entity as EntitySchema;
use Cycle\Schema\Definition\Field;
use Cycle\Schema\Definition\Relation;
use Cycle\Schema\Generator\SyncTables;
use Doctrine\Common\Annotations\AnnotationException as DoctrineException;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Inflector\Inflector;

final class Configurator
{
    /** @var AnnotationReader */
    private $reader;

    /**
     * @param AnnotationReader $reader
     */
    public function __construct(AnnotationReader $reader)
    {
        $this->reader = $reader;
    }

    /**
     * @param Entity           $ann
     * @param \ReflectionClass $class
     * @return EntitySchema
     */
    public function initEntity(Entity $ann, \ReflectionClass $class): EntitySchema
    {
        $e = new EntitySchema();
        $e->setClass($class->getName());

        $e->setRole($ann->getRole() ?? Inflector::camelize($class->getShortName()));

        // representing classes
        $e->setMapper($this->resolveName($ann->getMapper(), $class));
        $e->setRepository($this->resolveName($ann->getRepository(), $class));
        $e->setSource($this->resolveName($ann->getSource(), $class));
        $e->setConstrain($this->resolveName($ann->getConstrain(), $class));

        if ($ann->isReadonlySchema()) {
            $e->getOptions()->set(SyncTables::READONLY_SCHEMA, true);
        }

        return $e;
    }

    /**
     * @param Embeddable       $emb
     * @param \ReflectionClass $class
     * @return EntitySchema
     */
    public function initEmbedding(Embeddable $emb, \ReflectionClass $class): EntitySchema
    {
        $e = new EntitySchema();
        $e->setClass($class->getName());

        $e->setRole($emb->getRole() ?? Inflector::camelize($class->getShortName()));

        // representing classes
        $e->setMapper($this->resolveName($emb->getMapper(), $class));

        return $e;
    }

    /**
     * @param EntitySchema     $entity
     * @param \ReflectionClass $class
     * @param string           $columnPrefix
     */
    public function initFields(EntitySchema $entity, \ReflectionClass $class, string $columnPrefix = ''): void
    {
        foreach ($class->getProperties() as $property) {
            try {
                /** @var Column $column */
                $column = $this->reader->getPropertyAnnotation($property, Column::class);
            } catch (DoctrineException $e) {
                throw new AnnotationException($e->getMessage(), $e->getCode(), $e);
            }

            if ($column === null) {
                continue;
            }

            $entity->getFields()->set(
                $property->getName(),
                $this->initField($property->getName(), $column, $class, $columnPrefix)
            );
        }
    }

    /**
     * @param EntitySchema     $entity
     * @param \ReflectionClass $class
     */
    public function initRelations(EntitySchema $entity, \ReflectionClass $class): void
    {
        foreach ($class->getProperties() as $property) {
            try {
                $annotations = $this->reader->getPropertyAnnotations($property);
            } catch (DoctrineException $e) {
                throw new AnnotationException($e->getMessage(), $e->getCode(), $e);
            }

            foreach ($annotations as $ra) {
                if (!$ra instanceof RelationAnnotation\RelationInterface) {
                    continue;
                }

                if ($ra->getTarget() === null) {
                    throw new AnnotationException(
                        "Relation target definition is required on `{$entity->getClass()}`"
                    );
                }

                $relation = new Relation();
                $relation->setTarget($this->resolveName($ra->getTarget(), $class));
                $relation->setType($ra->getType());

                $inverse = $ra->getInverse();
                if ($inverse !== null) {
                    $relation->setInverse(
                        $inverse->getName(),
                        $inverse->getType(),
                        $inverse->getLoadMethod()
                    );
                }

                foreach ($ra->getOptions() as $option => $value) {
                    if ($option === 'though') {
                        $value = $this->resolveName($value, $class);
                    }

                    $relation->getOptions()->set($option, $value);
                }

                // need relation definition
                $entity->getRelations()->set($property->getName(), $relation);
            }
        }
    }

    /**
     * @param EntitySchema     $entity
     * @param Column[]         $columns
     * @param \ReflectionClass $class
     */
    public function initColumns(EntitySchema $entity, array $columns, \ReflectionClass $class): void
    {
        foreach ($columns as $name => $column) {
            if ($column->getColumn() === null && is_numeric($name)) {
                throw new AnnotationException(
                    "Column name definition is required on `{$entity->getClass()}`"
                );
            }

            if ($column->getType() === null) {
                throw new AnnotationException(
                    "Column type definition is required on `{$entity->getClass()}`"
                );
            }

            $entity->getFields()->set(
                $name ?? $column->getColumn(),
                $this->initField($column->getColumn() ?? $name, $column, $class, '')
            );
        }
    }

    /**
     * @param string           $name
     * @param Column           $column
     * @param \ReflectionClass $class
     * @param string           $columnPrefix
     * @return Field
     */
    public function initField(string $name, Column $column, \ReflectionClass $class, string $columnPrefix): Field
    {
        if ($column->getType() === null) {
            throw new AnnotationException(
                "Column type definition is required on `{$class->getName()}`.`{$name}`"
            );
        }

        $field = new Field();

        $field->setType($column->getType());
        $field->setColumn($columnPrefix . ($column->getColumn() ?? Inflector::tableize($name)));
        $field->setPrimary($column->isPrimary());

        $field->setTypecast($this->resolveTypecast($column->getTypecast(), $class));

        if ($column->isNullable()) {
            $field->getOptions()->set(\Cycle\Schema\Table\Column::OPT_NULLABLE, true);
            $field->getOptions()->set(\Cycle\Schema\Table\Column::OPT_DEFAULT, null);
        }

        if ($column->hasDefault()) {
            $field->getOptions()->set(\Cycle\Schema\Table\Column::OPT_DEFAULT, $column->getDefault());
        }

        if ($column->castDefault()) {
            $field->getOptions()->set(\Cycle\Schema\Table\Column::OPT_CAST_DEFAULT, true);
        }

        return $field;
    }

    /**
     * Resolve class or role name relative to the current class.
     *
     * @param string           $name
     * @param \ReflectionClass $class
     * @return string
     */
    public function resolveName(?string $name, \ReflectionClass $class): ?string
    {
        if ($name === null || class_exists($name, true) || interface_exists($name, true)) {
            return $name;
        }

        $resolved = sprintf(
            '%s\\%s',
            $class->getNamespaceName(),
            ltrim(str_replace('/', '\\', $name), '\\')
        );

        if (class_exists($resolved, true) || interface_exists($resolved, true)) {
            return ltrim($resolved, '\\');
        }

        return $name;
    }

    /**
     * @param mixed            $typecast
     * @param \ReflectionClass $class
     * @return mixed
     */
    protected function resolveTypecast($typecast, \ReflectionClass $class)
    {
        if (is_string($typecast) && strpos($typecast, '::') !== false) {
            // short definition
            $typecast = explode('::', $typecast);

            // resolve class name
            $typecast[0] = $this->resolveName($typecast[0], $class);
        }

        if (is_string($typecast)) {
            $typecast = $this->resolveName($typecast, $class);
            if (class_exists($typecast)) {
                $typecast = [$typecast, 'typecast'];
            }
        }

        return $typecast;
    }
}
