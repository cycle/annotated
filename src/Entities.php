<?php

declare(strict_types=1);

namespace Cycle\Annotated;

use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Exception\AnnotationException;
use Cycle\Annotated\Locator\EntityLocatorInterface;
use Cycle\Annotated\Utils\EntityUtils;
use Cycle\Schema\Definition\Entity as EntitySchema;
use Cycle\Schema\Exception\RegistryException;
use Cycle\Schema\Exception\RelationException;
use Cycle\Schema\GeneratorInterface;
use Cycle\Schema\Registry;
use Doctrine\Common\Annotations\Reader as DoctrineReader;
use Spiral\Attributes\ReaderInterface;

/**
 * Generates ORM schema based on annotated classes.
 */
final class Entities implements GeneratorInterface
{
    // table name generation
    public const TABLE_NAMING_PLURAL = 1;
    public const TABLE_NAMING_SINGULAR = 2;
    public const TABLE_NAMING_NONE = 3;

    private readonly ReaderInterface $reader;
    private readonly Configurator $generator;
    private readonly EntityUtils $utils;

    public function __construct(
        private readonly EntityLocatorInterface $locator,
        DoctrineReader|ReaderInterface|null $reader = null,
        int $tableNamingStrategy = self::TABLE_NAMING_PLURAL,
    ) {
        $this->reader = ReaderFactory::create($reader);
        $this->utils = new EntityUtils($this->reader);
        $this->generator = new Configurator($this->reader, $tableNamingStrategy);
    }

    public function run(Registry $registry): Registry
    {
        /** @var EntitySchema[] $children */
        $children = [];
        foreach ($this->locator->getEntities() as $entity) {
            $e = $this->generator->initEntity($entity->attribute, $entity->class);

            // columns
            $this->generator->initFields($e, $entity->class);

            // relations
            $this->generator->initRelations($e, $entity->class);

            // schema modifiers
            $this->generator->initModifiers($e, $entity->class);

            // foreign keys
            $this->generator->initForeignKeys($entity->attribute, $e, $entity->class);

            // additional columns (mapped to local fields automatically)
            $this->generator->initColumns($e, $entity->attribute->getColumns(), $entity->class);

            /** @var class-string $class */
            $class = $e->getClass();
            if ($this->utils->hasParent($class)) {
                foreach ($this->utils->findParents($class) as $parent) {
                    // additional columns from parent class
                    $ann = $this->reader->firstClassMetadata($parent, Entity::class);
                    if ($ann !== null) {
                        $this->generator->initColumns($e, $ann->getColumns(), $parent);
                    }
                }

                $children[] = $e;
                continue;
            }

            // generated fields
            $this->generator->initGeneratedFields($e, $entity->class);

            // register entity (OR find parent)
            $registry->register($e);

            $tableName = $e->getTableName();
            \assert(!empty($tableName));
            $registry->linkTable($e, $e->getDatabase(), $tableName);
        }

        foreach ($children as $e) {
            $class = $e->getClass();
            \assert($class !== null);

            $registry->registerChildWithoutMerge($registry->getEntity($this->utils->getParent($class)), $e);
        }

        return $this->normalizeNames($registry);
    }

    private function normalizeNames(Registry $registry): Registry
    {
        // resolve all the relation target names into roles
        foreach ($this->locator->getEntities() as $entity) {
            if (!$registry->hasEntity($entity->class->getName())) {
                continue;
            }

            $e = $registry->getEntity($entity->class->getName());

            // resolve all the relation target names into roles
            foreach ($e->getRelations() as $name => $r) {
                try {
                    $r->setTarget($this->resolveTarget($registry, $r->getTarget()));

                    if ($r->getOptions()->has('through')) {
                        $through = $r->getOptions()->get('through');
                        if ($through !== null) {
                            $r->getOptions()->set(
                                'through',
                                $this->resolveTarget($registry, $through),
                            );
                        }
                    }

                    if ($r->getOptions()->has('throughInnerKey')) {
                        if ($throughInnerKey = (array) $r->getOptions()->get('throughInnerKey')) {
                            $r->getOptions()->set('throughInnerKey', $throughInnerKey);
                        }
                    }

                    if ($r->getOptions()->has('throughOuterKey')) {
                        if ($throughOuterKey = (array) $r->getOptions()->get('throughOuterKey')) {
                            $r->getOptions()->set('throughOuterKey', $throughOuterKey);
                        }
                    }
                } catch (RegistryException $ex) {
                    /** @var non-empty-string $role */
                    $role = $e->getRole();

                    throw new RelationException(
                        \sprintf('Unable to resolve `%s`.`%s` relation target (not found or invalid)', $role, $name),
                        $ex->getCode(),
                        $ex,
                    );
                }
            }

            // resolve foreign key target and column names
            foreach ($e->getForeignKeys() as $foreignKey) {
                $target = $this->resolveTarget($registry, $foreignKey->getTarget());
                \assert(!empty($target), 'Unable to resolve foreign key target entity.');
                $targetEntity = $registry->getEntity($target);

                $foreignKey->setTarget($target);
                $foreignKey->setInnerColumns($this->getColumnNames($e, $foreignKey->getInnerColumns()));

                $foreignKey->setOuterColumns(empty($foreignKey->getOuterColumns())
                    ? $targetEntity->getPrimaryFields()->getColumnNames()
                    : $this->getColumnNames($targetEntity, $foreignKey->getOuterColumns()));
            }
        }

        return $registry;
    }

    /**
     * @return non-empty-string
     */
    private function resolveTarget(Registry $registry, string $name): string
    {
        if (\interface_exists($name, true)) {
            // do not resolve interfaces
            return $name;
        }

        $target = static function (EntitySchema $entity): string {
            $role = $entity->getRole();
            \assert($role !== null);

            return $role;
        };

        if (!$registry->hasEntity($name)) {
            // point all relations to the parent
            foreach ($registry as $entity) {
                foreach ($registry->getChildren($entity) as $child) {
                    if ($child->getClass() === $name || $child->getRole() === $name) {
                        return $target($entity);
                    }
                }
            }
        }

        return $target($registry->getEntity($name));
    }

    /**
     * @param array<non-empty-string> $columns
     *
     * @return array<non-empty-string>
     * @throws AnnotationException
     *
     */
    private function getColumnNames(EntitySchema $entity, array $columns): array
    {
        $names = [];
        foreach ($columns as $name) {
            $names[] = match (true) {
                $entity->getFields()->has($name) => $entity->getFields()->get($name)->getColumn(),
                $entity->getFields()->hasColumn($name) => $name,
                default => throw new AnnotationException('Unable to resolve column name.'),
            };
        }

        return $names;
    }
}
