<?php

declare(strict_types=1);

namespace Cycle\Annotated;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Embeddable;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\ForeignKey;
use Cycle\Annotated\Annotation\GeneratedValue;
use Cycle\Annotated\Annotation\Relation as RelationAnnotation;
use Cycle\Annotated\Exception\AnnotationException;
use Cycle\Annotated\Exception\AnnotationRequiredArgumentsException;
use Cycle\Annotated\Exception\AnnotationWrongTypeArgumentException;
use Cycle\Annotated\Utils\EntityUtils;
use Cycle\ORM\Schema\GeneratedField;
use Cycle\Schema\Definition\Entity as EntitySchema;
use Cycle\Schema\Definition\Field;
use Cycle\Schema\Definition\ForeignKey as ForeignKeySchema;
use Cycle\Schema\Definition\Relation;
use Cycle\Schema\Generator\SyncTables;
use Cycle\Schema\SchemaModifierInterface;
use Doctrine\Common\Annotations\Reader as DoctrineReader;
use Doctrine\Inflector\Inflector;
use Doctrine\Inflector\Rules\English\InflectorFactory;
use Spiral\Attributes\ReaderInterface;

final class Configurator
{
    private readonly ReaderInterface $reader;
    private readonly Inflector $inflector;
    private readonly EntityUtils $utils;

    public function __construct(
        DoctrineReader|ReaderInterface $reader,
        private readonly int $tableNamingStrategy = Entities::TABLE_NAMING_PLURAL,
    ) {
        $this->reader = ReaderFactory::create($reader);
        $this->inflector = (new InflectorFactory())->build();
        $this->utils = new EntityUtils($this->reader);
    }

    public function initEntity(Entity $ann, \ReflectionClass $class): EntitySchema
    {
        $e = new EntitySchema();
        $e->setClass($class->getName());

        $role = $ann->getRole() ?? $this->inflector->camelize($class->getShortName());
        \assert(!empty($role));
        $e->setRole($role);

        // representing classes
        $e->setMapper($this->resolveName($ann->getMapper(), $class));
        $e->setRepository($this->resolveName($ann->getRepository(), $class));
        $e->setSource($this->resolveName($ann->getSource(), $class));
        $e->setScope($this->resolveName($ann->getScope(), $class));
        $e->setDatabase($ann->getDatabase());

        $tableName = $ann->getTable() ?? $this->utils->tableName($role, $this->tableNamingStrategy);
        \assert(!empty($tableName));
        $e->setTableName($tableName);

        $typecast = $ann->getTypecast();
        if (\is_array($typecast)) {
            /** @var non-empty-string[] $typecast */
            $typecast = \array_map(fn (string $value): string => $this->resolveName($value, $class), $typecast);
        } else {
            /** @var non-empty-string|null $typecast */
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

        $role = $emb->getRole() ?? $this->inflector->camelize($class->getShortName());
        \assert(!empty($role));
        $e->setRole($role);

        // representing classes
        $e->setMapper($this->resolveName($emb->getMapper(), $class));

        return $e;
    }

    public function initFields(EntitySchema $entity, \ReflectionClass $class, string $columnPrefix = ''): void
    {
        foreach ($class->getProperties() as $property) {
            try {
                $column = $this->reader->firstPropertyMetadata($property, Column::class);
            } catch (\Exception $e) {
                throw new AnnotationException($e->getMessage(), (int) $e->getCode(), $e);
            } catch (\ArgumentCountError $e) {
                throw AnnotationRequiredArgumentsException::createFor($property, Column::class, $e);
            } catch (\TypeError $e) {
                throw AnnotationWrongTypeArgumentException::createFor($property, $e);
            }

            if ($column === null) {
                continue;
            }

            $field = $this->initField($property->getName(), $column, $class, $columnPrefix);
            $field->setEntityClass($property->getDeclaringClass()->getName());
            $entity->getFields()->set($property->getName(), $field);
        }
    }

    public function initRelations(EntitySchema $entity, \ReflectionClass $class): void
    {
        foreach ($class->getProperties() as $property) {
            $metadata = $this->getPropertyMetadata($property, RelationAnnotation\RelationInterface::class);

            foreach ($metadata as $meta) {
                \assert($meta instanceof RelationAnnotation\RelationInterface);

                $target = $meta->getTarget();
                if ($target === null) {
                    throw new AnnotationException(
                        "Relation target definition is required on `{$entity->getClass()}`.`{$property->getName()}`"
                    );
                }

                $relation = new Relation();

                $resolvedTarget = $this->resolveName($target, $class);
                \assert(!empty($resolvedTarget));
                $relation->setTarget($resolvedTarget);

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

                if ($meta instanceof RelationAnnotation\Embedded && $meta->getPrefix() === null) {
                    /** @var class-string $target */
                    $target = $relation->getTarget();
                    /** @var Embeddable|null $embeddable */
                    $embeddable = $this->reader->firstClassMetadata(new \ReflectionClass($target), Embeddable::class);
                    if ($embeddable !== null) {
                        $meta->setPrefix($embeddable->getColumnPrefix());
                    }
                }

                foreach ($meta->getOptions() as $option => $value) {
                    $value = match ($option) {
                        'collection' => $this->resolveName($value, $class),
                        'through' => $this->resolveName($value, $class),
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
        $metadata = $this->getClassMetadata($class, SchemaModifierInterface::class);

        foreach ($metadata as $meta) {
            \assert($meta instanceof SchemaModifierInterface);

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
            $isNumericKey = \is_numeric($key);
            $propertyName = $column->getProperty();

            if (!$isNumericKey && $propertyName !== null && $key !== $propertyName) {
                throw new AnnotationException(
                    "Can not use name \"{$key}\" for Column of the `{$entity->getRole()}` role, because the "
                    . "\"property\" field of the metadata class has already been set to \"{$propertyName}\"."
                );
            }

            $propertyName ??= $isNumericKey ? null : $key;
            $columnName = $column->getColumn() ?? $propertyName;
            $propertyName ??= $columnName;
            \assert(!empty($propertyName));

            if ($columnName === null) {
                throw new AnnotationException(
                    "Column name definition is required on `{$entity->getClass()}`"
                );
            }

            $field = $this->initField($columnName, $column, $class, '');
            $field->setEntityClass($entity->getClass());
            $entity->getFields()->set($propertyName, $field);
        }
    }

    public function initField(string $name, Column $column, \ReflectionClass $class, string $columnPrefix): Field
    {
        $field = new Field();

        $field->setType($column->getType());

        $columnName = $columnPrefix . ($column->getColumn() ?? $this->inflector->tableize($name));
        \assert(!empty($columnName));
        $field->setColumn($columnName);

        $field->setPrimary($column->isPrimary());
        if ($this->isOnInsertGeneratedField($field)) {
            $field->setGenerated(GeneratedField::ON_INSERT);
        }

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

        if ($column->isReadonlySchema()) {
            $field->getAttributes()->set('readonlySchema', true);
        }

        foreach ($column->getAttributes() as $k => $v) {
            $field->getAttributes()->set($k, $v);
        }

        return $field;
    }

    public function initForeignKeys(Entity $ann, EntitySchema $entity, \ReflectionClass $class): void
    {
        $foreignKeys = [];
        foreach ($ann->getForeignKeys() as $foreignKey) {
            $foreignKeys[] = $foreignKey;
        }

        foreach ($this->getClassMetadata($class, ForeignKey::class) as $foreignKey) {
            $foreignKeys[] = $foreignKey;
        }

        foreach ($class->getProperties() as $property) {
            foreach ($this->getPropertyMetadata($property, ForeignKey::class) as $foreignKey) {
                if ($foreignKey->innerKey === null) {
                    $foreignKey->innerKey = [$property->getName()];
                }
                $foreignKeys[] = $foreignKey;
            }
        }

        foreach ($foreignKeys as $foreignKey) {
            if ($foreignKey->innerKey === null) {
                throw new AnnotationException(
                    "Inner column definition for the foreign key is required on `{$entity->getClass()}`"
                );
            }

            $fk = new ForeignKeySchema();
            $fk->setTarget($foreignKey->target);
            $fk->setInnerColumns((array) $foreignKey->innerKey);
            $fk->setOuterColumns((array) $foreignKey->outerKey);
            $fk->createIndex($foreignKey->indexCreate);
            $fk->setAction($foreignKey->action);

            $entity->getForeignKeys()->set($fk);
        }
    }

    public function initGeneratedFields(EntitySchema $entity, \ReflectionClass $class): void
    {
        foreach ($class->getProperties() as $property) {
            try {
                $generated = $this->reader->firstPropertyMetadata($property, GeneratedValue::class);
                if ($generated !== null) {
                    $entity->getFields()->get($property->getName())->setGenerated($generated->getFlags());
                }
            } catch (\Throwable $e) {
                throw new AnnotationException($e->getMessage(), (int) $e->getCode(), $e);
            }
        }
    }

    /**
     * Resolve class or role name relative to the current class.
     *
     * @template TEntity of object
     *
     * @psalm-return($name is class-string<TEntity>
     *     ? class-string<TEntity>
     *     : ($name is string ? string : null)
     * )
     */
    public function resolveName(?string $name, \ReflectionClass $class): ?string
    {
        if ($name === null || $this->exists($name)) {
            return $name;
        }

        $resolved = \sprintf(
            '%s\\%s',
            $class->getNamespaceName(),
            \ltrim(\str_replace('/', '\\', $name), '\\')
        );

        if ($this->exists($resolved)) {
            return \ltrim($resolved, '\\');
        }

        return $name;
    }

    private function exists(string $name): bool
    {
        return \class_exists($name, true) || \interface_exists($name, true);
    }

    private function resolveTypecast(mixed $typecast, \ReflectionClass $class): mixed
    {
        if (\is_string($typecast) && \str_contains($typecast, '::')) {
            // short definition
            $typecast = \explode('::', $typecast);

            // resolve class name
            $typecast[0] = $this->resolveName($typecast[0], $class);
        }

        if (\is_string($typecast)) {
            $typecast = $this->resolveName($typecast, $class);
            if (\class_exists($typecast) && \method_exists($typecast, 'typecast')) {
                $typecast = [$typecast, 'typecast'];
            }
        }

        return $typecast;
    }

    /**
     * @template T
     *
     * @param class-string<T> $name
     *
     * @throws AnnotationException
     *
     * @return iterable<T>
     */
    private function getClassMetadata(\ReflectionClass $class, string $name): iterable
    {
        try {
            return $this->reader->getClassMetadata($class, $name);
        } catch (\Exception $e) {
            throw new AnnotationException($e->getMessage(), (int) $e->getCode(), $e);
        }
    }

    /**
     * @template T
     *
     * @param class-string<T> $name
     *
     * @throws AnnotationException
     *
     * @return iterable<T>
     */
    private function getPropertyMetadata(\ReflectionProperty $property, string $name): iterable
    {
        try {
            return $this->reader->getPropertyMetadata($property, $name);
        } catch (\Exception $e) {
            throw new AnnotationException($e->getMessage(), (int) $e->getCode(), $e);
        }
    }

    private function isOnInsertGeneratedField(Field $field): bool
    {
        return match ($field->getType()) {
            'serial', 'bigserial', 'smallserial' => true,
            default => $field->isPrimary()
        };
    }
}
