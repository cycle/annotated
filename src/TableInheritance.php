<?php

declare(strict_types=1);

namespace Cycle\Annotated;

use Cycle\Annotated\Annotation\Inheritance;
use Cycle\Annotated\Exception\AnnotationException;
use Cycle\Annotated\Utils\EntityUtils;
use Cycle\Schema\Definition\Entity as EntitySchema;
use Cycle\Schema\Definition\Inheritance\JoinedTable as JoinedTableInheritanceSchema;
use Cycle\Schema\Definition\Inheritance\SingleTable as SingleTableInheritanceSchema;
use Cycle\Schema\GeneratorInterface;
use Cycle\Schema\Registry;
use Doctrine\Common\Annotations\Reader as DoctrineReader;
use Spiral\Attributes\ReaderInterface;

class TableInheritance implements GeneratorInterface
{
    private ReaderInterface $reader;
    private EntityUtils $utils;

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

                    // Child entities always have parent entity
                    do {
                        $parent = $this->findParent(
                            $registry,
                            $this->utils->findParent($childClass, false)
                        );

                        if ($annotation instanceof Inheritance\JoinedTable) {
                            break;
                        }

                        $childClass = $parent->getClass();
                    } while ($this->parseMetadata($parent, Inheritance::class) !== null);

                    if ($entity = $this->initInheritance($annotation, $child, $parent)) {
                        $found[] = $entity;
                    }
                }

                // All child should be presented in a schema as separated entity
                // Every child will be handled according its table inheritance type
                if (!$registry->hasEntity($child->getRole())) {
                    $registry->register($child);

                    $registry->linkTable(
                        $child,
                        $child->getDatabase(),
                        $child->getTableName(),
                    );
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
            }
        }

        return $registry;
    }

    private function findParent(Registry $registry, string $role): ?EntitySchema
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

        return null;
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

            $parent->getInheritance()->addChild(
                $inheritance->getValue() ?? $entity->getRole(),
                $entity->getClass()
            );

            $entity->markAsChildOfSingleTableInheritance($parent->getClass());

            // Root STI may have a discriminator annotation
            /** @var Inheritance\DiscriminatorColumn $annotation */
            if ($annotation = $this->parseMetadata($parent, Inheritance\DiscriminatorColumn::class)) {
                $parent->getInheritance()->setDiscriminator($annotation->getName());
            }

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
            throw new AnnotationException($e->getMessage(), $e->getCode(), $e);
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
}
