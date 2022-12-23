<?php

declare(strict_types=1);

namespace Cycle\Annotated;

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

    private Configurator $generator;
    private EntityUtils $utils;

    public function __construct(
        private EntityLocatorInterface $locator,
        DoctrineReader|ReaderInterface $reader = null,
        int $tableNamingStrategy = self::TABLE_NAMING_PLURAL
    ) {
        $reader = ReaderFactory::create($reader);
        $this->utils = new EntityUtils($reader);
        $this->generator = new Configurator($reader, $tableNamingStrategy);
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

            // additional columns (mapped to local fields automatically)
            $this->generator->initColumns($e, $entity->attribute->getColumns(), $entity->class);

            if ($this->utils->hasParent($e->getClass())) {
                $children[] = $e;
                continue;
            }

            // register entity (OR find parent)
            $registry->register($e);
            $registry->linkTable($e, $e->getDatabase(), $e->getTableName());
        }

        foreach ($children as $e) {
            $registry->registerChild($registry->getEntity($this->utils->findParent($e->getClass())), $e);
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

            // relations
            foreach ($e->getRelations() as $name => $r) {
                try {
                    $r->setTarget($this->resolveTarget($registry, $r->getTarget()));

                    if ($r->getOptions()->has('though')) {
                        $though = $r->getOptions()->get('though');
                        if ($though !== null) {
                            $r->getOptions()->set(
                                'though',
                                $this->resolveTarget($registry, $though)
                            );
                        }
                    }

                    if ($r->getOptions()->has('through')) {
                        $through = $r->getOptions()->get('through');
                        if ($through !== null) {
                            $r->getOptions()->set(
                                'through',
                                $this->resolveTarget($registry, $through)
                            );
                        }
                    }

                    if ($r->getOptions()->has('throughInnerKey')) {
                        if ($throughInnerKey = (array)$r->getOptions()->get('throughInnerKey')) {
                            $r->getOptions()->set('throughInnerKey', $throughInnerKey);
                        }
                    }

                    if ($r->getOptions()->has('throughOuterKey')) {
                        if ($throughOuterKey = (array)$r->getOptions()->get('throughOuterKey')) {
                            $r->getOptions()->set('throughOuterKey', $throughOuterKey);
                        }
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

    private function resolveTarget(Registry $registry, string $name): ?string
    {
        if (\interface_exists($name, true)) {
            // do not resolve interfaces
            return $name;
        }

        if (! $registry->hasEntity($name)) {
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
}
