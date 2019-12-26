<?php

/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

declare(strict_types=1);

namespace Cycle\Annotated;

use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Exception\AnnotationException;
use Cycle\Schema\Definition\Entity as EntitySchema;
use Cycle\Schema\Exception\RegistryException;
use Cycle\Schema\Exception\RelationException;
use Cycle\Schema\GeneratorInterface;
use Cycle\Schema\Registry;
use Doctrine\Common\Annotations\AnnotationException as DoctrineException;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Inflector\Inflector;
use Spiral\Tokenizer\ClassesInterface;

/**
 * Generates ORM schema based on annotated classes.
 */
final class Entities implements GeneratorInterface
{
    // table name generation
    public const TABLE_NAMING_PLURAL   = 1;
    public const TABLE_NAMING_SINGULAR = 2;
    public const TABLE_NAMING_NONE     = 3;

    /** @var ClassesInterface */
    private $locator;

    /** @var AnnotationReader */
    private $reader;

    /** @var Configurator */
    private $generator;

    /** @var int */
    private $tableNaming;

    /**
     * @param ClassesInterface      $locator
     * @param AnnotationReader|null $reader
     * @param int                   $tableNaming
     */
    public function __construct(
        ClassesInterface $locator,
        AnnotationReader $reader = null,
        int $tableNaming = self::TABLE_NAMING_PLURAL
    ) {
        $this->locator = $locator;
        $this->reader = $reader ?? new AnnotationReader();
        $this->generator = new Configurator($this->reader);
        $this->tableNaming = $tableNaming;
    }

    /**
     * @param Registry $registry
     * @return Registry
     */
    public function run(Registry $registry): Registry
    {
        /** @var EntitySchema[] $children */
        $children = [];
        foreach ($this->locator->getClasses() as $class) {
            try {
                /** @var Entity $ann */
                $ann = $this->reader->getClassAnnotation($class, Entity::class);
            } catch (DoctrineException $e) {
                throw new AnnotationException($e->getMessage(), $e->getCode(), $e);
            }

            if ($ann === null) {
                continue;
            }

            $e = $this->generator->initEntity($ann, $class);

            // columns
            $this->generator->initFields($e, $class);

            // relations
            $this->generator->initRelations($e, $class);

            // additional columns (mapped to local fields automatically)
            $this->generator->initColumns($e, $ann->getColumns(), $class);

            if ($this->hasParent($registry, $e->getClass())) {
                $children[] = $e;
                continue;
            }

            // register entity (OR find parent)
            $registry->register($e);

            $registry->linkTable(
                $e,
                $ann->getDatabase(),
                $ann->getTable() ?? $this->tableName($e->getRole())
            );
        }

        foreach ($children as $e) {
            $registry->registerChild($registry->getEntity($this->findParent($registry, $e->getClass())), $e);
        }

        return $this->normalizeNames($registry);
    }

    /**
     * @param Registry $registry
     * @return Registry
     */
    protected function normalizeNames(Registry $registry): Registry
    {
        // resolve all the relation target names into roles
        foreach ($this->locator->getClasses() as $class) {
            if (!$registry->hasEntity($class->getName())) {
                continue;
            }

            $e = $registry->getEntity($class->getName());

            // relations
            foreach ($e->getRelations() as $name => $r) {
                try {
                    $r->setTarget($this->resolveTarget($registry, $r->getTarget()));

                    if ($r->getOptions()->has('though')) {
                        $r->getOptions()->set(
                            'though',
                            $this->resolveTarget($registry, $r->getOptions()->get('though'))
                        );
                    }
                } catch (RegistryException $ex) {
                    throw new RelationException(
                        sprintf(
                            'Unable to resolve `%s`.`%s` relation target (not found or invalid)',
                            $e->getRole(),
                            $name
                        ),
                        $ex->getCode(),
                        $ex
                    );
                }
            }
        }

        return $registry;
    }

    /**
     * @param Registry $registry
     * @param string   $name
     * @return string|null
     */
    protected function resolveTarget(Registry $registry, string $name): ?string
    {
        if (is_null($name) || interface_exists($name, true)) {
            // do not resolve interfaces
            return $name;
        }

        if (!$registry->hasEntity($name)) {
            // point all relations to the parent
            foreach ($registry as $entity) {
                foreach ($registry->getChildren($entity) as $child) {
                    if ($child->getClass() === $name || $child->getRole() === $name) {
                        return $entity->getRole();
                    }
                }
            }
        }

        return $registry->getEntity($name)->getRole();
    }

    /**
     * @param string $role
     * @return string
     */
    protected function tableName(string $role): string
    {
        $table = Inflector::tableize($role);

        switch ($this->tableNaming) {
            case self::TABLE_NAMING_PLURAL:
                return Inflector::pluralize(Inflector::tableize($role));

            case self::TABLE_NAMING_SINGULAR:
                return Inflector::singularize(Inflector::tableize($role));

            default:
                return $table;
        }
    }

    /**
     * @param Registry $registry
     * @param string   $class
     * @return bool
     */
    protected function hasParent(Registry $registry, string $class): bool
    {
        return $this->findParent($registry, $class) !== null;
    }

    /**
     * @param Registry $registry
     * @param string   $class
     * @return string|null
     */
    protected function findParent(Registry $registry, string $class): ?string
    {
        $parents = class_parents($class);

        foreach (array_reverse($parents) as $parent) {
            try {
                $class = new \ReflectionClass($parent);
            } catch (\ReflectionException $e) {
                continue;
            }

            if ($class->getDocComment() === false) {
                continue;
            }

            $ann = $this->reader->getClassAnnotation($class, Entity::class);
            if ($ann !== null) {
                return $parent;
            }
        }

        return null;
    }
}
