<?php

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
use Cycle\Schema\SchemaModifierInterface;
use Doctrine\Common\Annotations\Reader as DoctrineReader;
use Doctrine\Inflector\Inflector;
use Doctrine\Inflector\Rules\English\InflectorFactory;
use Exception;
use Spiral\Attributes\ReaderInterface;

final class Configurator
{
    private ReaderInterface $reader;

    private Inflector $inflector;

    public function __construct(DoctrineReader|ReaderInterface $reader)
    {
        $this->reader = ReaderFactory::create($reader);
        $this->inflector = (new InflectorFactory())->build();
    }

    public function initEntity(Entity $ann, \ReflectionClass $class): EntitySchema
    {
        $e = new EntitySchema();
        $e->setClass($class->getName());

        $e->setRole($ann->getRole() ?? $this->inflector->camelize($class->getShortName()));

        // representing classes
        $e->setMapper($this->resolveName($ann->getMapper(), $class));
        $e->setRepository($this->resolveName($ann->getRepository(), $class));
        $e->setSource($this->resolveName($ann->getSource(), $class));
        $e->setScope($this->resolveName($ann->getScope(), $class));

        $typecast = $ann->getTypecast();
        if (is_array($typecast)) {
            $typecast = array_map(fn (string $value): string => $this->resolveName($value, $class), $typecast);
        } else {
            $typecast = $this->resolveName($typecast, $class);
        }

        $e->setTypecast($typecast);

        if ($ann->isReadonlySchema()) {
            $e->getOptions()->set(SyncTables::READONLY_SCHEMA, true);
        }

        return $e;
    }

    public function initEmbedding(Embeddable $emb, \ReflectionClass $class): EntitySchema
    {
        $e = new EntitySchema();
        $e->setClass($class->getName());

        $e->setRole($emb->getRole() ?? $this->inflector->camelize($class->getShortName()));

        // representing classes
        $e->setMapper($this->resolveName($emb->getMapper(), $class));

        return $e;
    }

    public function initFields(EntitySchema $entity, \ReflectionClass $class, string $columnPrefix = ''): void
    {
        foreach ($class->getProperties() as $property) {
            try {
                $column = $this->reader->firstPropertyMetadata($property, Column::class);
            } catch (Exception $e) {
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

    public function initRelations(EntitySchema $entity, \ReflectionClass $class): void
    {
        foreach ($class->getProperties() as $property) {
            try {
                $metadata = $this->reader->getPropertyMetadata($property, RelationAnnotation\RelationInterface::class);
            } catch (Exception $e) {
                throw new AnnotationException($e->getMessage(), $e->getCode(), $e);
            }

            foreach ($metadata as $meta) {
                assert($meta instanceof RelationAnnotation\RelationInterface);

                if ($meta->getTarget() === null) {
                    throw new AnnotationException(
                        "Relation target definition is required on `{$entity->getClass()}`.`{$property->getName()}`"
                    );
                }

                $relation = new Relation();
                $relation->setTarget($this->resolveName($meta->getTarget(), $class));
                $relation->setType($meta->getType());

                $inverse = $meta->getInverse() ?? $this->reader->firstPropertyMetadata(
                    $property,
                    RelationAnnotation\Inverse::class
                );
                if ($inverse !== null) {
                    $relation->setInverse(
                        $inverse->getName(),
                        $inverse->getType(),
                        $inverse->getLoadMethod()
                    );
                }

                foreach ($meta->getOptions() as $option => $value) {
                    $value = match ($option) {
                        'collection' => $this->resolveName($value, $class),
                        'though', 'through' => $this->resolveName($value, $class),
                        default => $value
                    };

                    $relation->getOptions()->set($option, $value);
                }

                // need relation definition
                $entity->getRelations()->set($property->getName(), $relation);
            }
        }
    }

    public function initModifiers(EntitySchema $entity, \ReflectionClass $class): void
    {
        try {
            $metadata = $this->reader->getClassMetadata($class, SchemaModifierInterface::class);
        } catch (Exception $e) {
            throw new AnnotationException($e->getMessage(), $e->getCode(), $e);
        }

        foreach ($metadata as $meta) {
            assert($meta instanceof SchemaModifierInterface);

            // need relation definition
            $entity->addSchemaModifier($meta);
        }
    }

    /**
     * @param Column[] $columns
     */
    public function initColumns(EntitySchema $entity, array $columns, \ReflectionClass $class): void
    {
        foreach ($columns as $key => $column) {
            $isNumericKey = is_numeric($key);
            $propertyName = $column->getProperty();

            if (!$isNumericKey && $propertyName !== null && $key !== $propertyName) {
                throw new AnnotationException(
                    "Can not use name \"{$key}\" for Column of the `{$entity->getRole()}` role, because the "
                    . "\"property\" field of the metadata class has already been set to \"{$propertyName}\"."
                );
            }

            $propertyName = $propertyName ?? ($isNumericKey ? null : $key);
            $columnName = $column->getColumn() ?? $propertyName;
            $propertyName = $propertyName ?? $columnName;

            if ($columnName === null) {
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
                $propertyName,
                $this->initField($columnName, $column, $class, '')
            );
        }
    }

    public function initField(string $name, Column $column, \ReflectionClass $class, string $columnPrefix): Field
    {
        if ($column->getType() === null) {
            throw new AnnotationException(
                "Column type definition is required on `{$class->getName()}`.`{$name}`."
            );
        }

        $field = new Field();

        $field->setType($column->getType());
        $field->setColumn($columnPrefix . ($column->getColumn() ?? $this->inflector->tableize($name)));
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

    private function resolveTypecast(mixed $typecast, \ReflectionClass $class): mixed
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
