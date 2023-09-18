<?php

declare(strict_types=1);

namespace Cycle\Annotated;

use Cycle\Annotated\Annotation\Inheritance;
use Cycle\Annotated\Exception\AnnotationException;
use Cycle\Annotated\Utils\EntityUtils;
use Cycle\Schema\Definition\Entity as EntitySchema;
use Cycle\Schema\Definition\Inheritance\JoinedTable as JoinedTableInheritanceSchema;
use Cycle\Schema\Definition\Inheritance\SingleTable as SingleTableInheritanceSchema;
use Cycle\Schema\Definition\Map\FieldMap;
use Cycle\Schema\Exception\TableInheritance\WrongParentKeyColumnException;
use Cycle\Schema\GeneratorInterface;
use Cycle\Schema\Registry;
use Doctrine\Common\Annotations\Reader as DoctrineReader;
use Spiral\Attributes\ReaderInterface;

class TableInheritance implements GeneratorInterface
{
    private readonly ReaderInterface $reader;
    private readonly EntityUtils $utils;

    public function __construct(
        DoctrineReader|ReaderInterface $reader = null
    ) {
        $this->reader = ReaderFactory::create($reader);
        $this->utils = new EntityUtils($this->reader);
    }

    public function run(Registry $registry): Registry
    {
        /** @var EntitySchema[] $found */
        $found = [];

        foreach ($registry as $entity) {
            // Only child entities can have table inheritance annotation
            $children = $registry->getChildren($entity);

            foreach ($children as $child) {
                /** @var Inheritance $annotation */
                if ($annotation = $this->parseMetadata($child, Inheritance::class)) {
                    $childClass = $child->getClass();
                    \assert($childClass !== null);

                    // Child entities always have parent entity
                    do {
                        $parent = $this->getParent(
                            $registry,
                            $this->utils->getParent($childClass, false)
                        );

                        if ($annotation instanceof Inheritance\JoinedTable) {
                            break;
                        }

                        $childClass = $parent->getClass();
                        \assert($childClass !== null);
                    } while ($this->parseMetadata($parent, Inheritance::class) !== null);

                    if ($inheritanceEntity = $this->initInheritance($annotation, $child, $parent)) {
                        $found[] = $inheritanceEntity;
                    }
                }

                // All child should be presented in a schema as separated entity
                // Every child will be handled according its table inheritance type
                \assert($child->getRole() !== null && $entity !== null && isset($parent));
                if (!$registry->hasEntity($child->getRole())) {
                    $registry->register($child);

                    $tableName = $this->getTableName($child, $registry);
                    \assert(!empty($tableName));

                    $registry->linkTable($child, $this->getDatabase($child, $registry), $tableName);
                }
            }
        }

        foreach ($found as $entity) {
            if ($entity->getInheritance() instanceof SingleTableInheritanceSchema) {
                $allowedEntities = \array_map(
                    static fn (string $role) => $registry->getEntity($role)->getClass(),
                    $entity->getInheritance()->getChildren()
                );
                $this->removeStiExtraFields($entity, $allowedEntities);
            } elseif ($entity->getInheritance() instanceof JoinedTableInheritanceSchema) {
                $this->removeJtiExtraFields($entity);

                $joinedTable = $this->parseMetadata($entity, Inheritance::class);
                \assert($joinedTable instanceof Inheritance\JoinedTable);

                $this->addForeignKey($joinedTable, $entity, $registry);
            }
        }

        return $registry;
    }

    private function getParent(Registry $registry, string $role): EntitySchema
    {
        foreach ($registry as $entity) {
            if ($entity->getRole() === $role || $entity->getClass() === $role) {
                return $entity;
            }

            $children = $registry->getChildren($entity);
            foreach ($children as $child) {
                if ($child->getRole() === $role || $child->getClass() === $role) {
                    return $child;
                }
            }
        }

        throw new AnnotationException(\sprintf('The entity could not be found for the role `%s`.', $role));
    }

    private function initInheritance(
        Inheritance $inheritance,
        EntitySchema $entity,
        EntitySchema $parent
    ): ?EntitySchema {
        if ($inheritance instanceof Inheritance\SingleTable) {
            if (!$parent->getInheritance() instanceof SingleTableInheritanceSchema) {
                $parent->setInheritance(new SingleTableInheritanceSchema());
            }

            $class = $entity->getClass();
            \assert($class !== null);

            /** @var non-empty-string $discriminatorValue */
            $discriminatorValue = $inheritance->getValue() ?? $entity->getRole();
            \assert(!empty($discriminatorValue));

            /** @var SingleTableInheritanceSchema $parentInheritance */
            $parentInheritance = $parent->getInheritance();
            $parentInheritance->addChild($discriminatorValue, $class);

            $entity->markAsChildOfSingleTableInheritance($parent->getClass());

            // Root STI may have a discriminator annotation
            /** @var Inheritance\DiscriminatorColumn $annotation */
            if ($annotation = $this->parseMetadata($parent, Inheritance\DiscriminatorColumn::class)) {
                $parentInheritance->setDiscriminator($annotation->getName());
            }

            $parent->merge($entity);

            return $parent;
        }
        if ($inheritance instanceof Inheritance\JoinedTable) {
            $entity->setInheritance(
                new JoinedTableInheritanceSchema(
                    $parent,
                    $inheritance->getOuterKey()
                )
            );

            return $entity;
        }

        // Custom table inheritance types developers can handle in their own generators
        return null;
    }

    /**
     * @template T
     *
     * @param class-string<T> $name
     *
     * @return T|null
     */
    private function parseMetadata(EntitySchema $entity, string $name): ?object
    {
        try {
            $class = $entity->getClass();
            assert($class !== null);
            return $this->reader->firstClassMetadata(new \ReflectionClass($class), $name);
        } catch (\Exception $e) {
            throw new AnnotationException($e->getMessage(), (int) $e->getCode(), $e);
        }
    }

    /**
     * Removes parent entity fields from given entity except Primary key.
     */
    private function removeJtiExtraFields(EntitySchema $entity): void
    {
        foreach ($entity->getFields() as $name => $field) {
            if ($field->getEntityClass() === $entity->getClass()) {
                continue;
            }

            if (!$field->isPrimary()) {
                $entity->getFields()->remove($name);
            } else {
                if ($field->getType() === 'primary') {
                    $field->setType('integer')->setPrimary(true);
                } elseif ($field->getType() === 'bigPrimary') {
                    $field->setType('bigInteger')->setPrimary(true);
                }
            }
        }
    }

    /**
     * Removes non STI child entity fields from given entity.
     */
    private function removeStiExtraFields(EntitySchema $entity, array $allowedEntities): void
    {
        $allowedEntities[] = $entity->getClass();

        foreach ($entity->getFields() as $name => $field) {
            if (\in_array($field->getEntityClass(), $allowedEntities, true)) {
                continue;
            }

            $entity->getFields()->remove($name);
        }
    }

    private function addForeignKey(
        Inheritance\JoinedTable $annotation,
        EntitySchema $entity,
        Registry $registry
    ): void {
        if (!$annotation->isCreateFk()) {
            return;
        }

        $parent = $this->getParentForForeignKey($entity, $registry);
        $outerFields = $this->getOuterFields($entity, $parent, $annotation);

        foreach ($outerFields->getColumnNames() as $column) {
            if (!$registry->getTableSchema($parent)->hasColumn($column)) {
                return;
            }
        }

        foreach ($entity->getPrimaryFields()->getColumnNames() as $column) {
            if (!$registry->getTableSchema($entity)->hasColumn($column)) {
                return;
            }
        }

        $registry->getTableSchema($parent)->index($outerFields->getColumnNames())->unique();

        $registry->getTableSchema($entity)
            ->foreignKey($entity->getPrimaryFields()->getColumnNames())
            ->references($registry->getTable($parent), $outerFields->getColumnNames())
            ->onUpdate($annotation->getFkAction())
            ->onDelete($annotation->getFkAction());
    }

    private function getParentForForeignKey(EntitySchema $schema, Registry $registry): EntitySchema
    {
        $parentSchema = $schema->getInheritance();

        if ($parentSchema instanceof JoinedTableInheritanceSchema) {
            // entity is STI child
            $parent = $parentSchema->getParent();
            if ($parent->isChildOfSingleTableInheritance()) {
                $parentClass = $parent->getClass();
                \assert($parentClass !== null);

                return $this->getParentForForeignKey($this->getParent(
                    $registry,
                    $this->utils->getParent($parentClass, false)
                ), $registry);
            }

            return $parent;
        }

        return $schema;
    }

    private function getOuterFields(
        EntitySchema $entity,
        EntitySchema $parent,
        Inheritance\JoinedTable $annotation
    ): FieldMap {
        $outerKey = $annotation->getOuterKey();

        if ($outerKey) {
            if (!$parent->getFields()->has($outerKey)) {
                throw new WrongParentKeyColumnException($entity, $outerKey);
            }

            return (new FieldMap())->set($outerKey, $parent->getFields()->get($outerKey));
        }

        return $parent->getPrimaryFields();
    }

    private function getTableName(EntitySchema $child, Registry $registry): ?string
    {
        $childClass = $child->getClass();
        \assert($childClass !== null);

        $parent = $this->getParent($registry, $this->utils->getParent($childClass, false));

        $inheritance = $parent->getInheritance();
        if (!$inheritance instanceof SingleTableInheritanceSchema) {
            return $child->getTableName();
        }
        $entities = \array_map(
            static fn (string $role) => $registry->getEntity($role)->getClass(),
            $inheritance->getChildren()
        );

        return \in_array($childClass, $entities, true) ? $parent->getTableName() : $child->getTableName();
    }

    private function getDatabase(EntitySchema $child, Registry $registry): ?string
    {
        $childClass = $child->getClass();
        \assert($childClass !== null);

        $parent = $this->getParent($registry, $this->utils->getParent($childClass, false));

        $inheritance = $parent->getInheritance();
        if (!$inheritance instanceof SingleTableInheritanceSchema) {
            return $child->getDatabase();
        }
        $entities = \array_map(
            static fn (string $role) => $registry->getEntity($role)->getClass(),
            $inheritance->getChildren()
        );

        return \in_array($childClass, $entities, true) ? $parent->getDatabase() : $child->getDatabase();
    }
}
