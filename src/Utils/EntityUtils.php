<?php

declare(strict_types=1);

namespace Cycle\Annotated\Utils;

use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Entities;
use Cycle\Schema\Registry;
use Doctrine\Inflector\Rules\English\InflectorFactory;
use Spiral\Attributes\ReaderInterface;

/**
 * @internal
 */
class EntityUtils
{
    private \Doctrine\Inflector\Inflector $inflector;

    public function __construct(private ReaderInterface $reader)
    {
        $this->inflector = (new InflectorFactory())->build();
    }

    /**
     * @param class-string $class
     */
    public function hasParent(string $class, bool $root = true): bool
    {
        return $this->findParent($class, $root) !== null;
    }

    /**
     * @param class-string $class
     *
     * @return class-string|null
     */
    public function findParent(string $class, bool $root = true): ?string
    {
        /** @var \ReflectionClass[] $parents */
        $parents = $this->findParents($class);

        $parents = $root ? \array_reverse($parents) : $parents;

        return isset($parents[0]) ? $parents[0]->getName() : null;
    }

    public function findParents(string $class): array
    {
        $parents = [];
        /** @var class-string[] $classParents */
        $classParents = \class_parents($class);

        foreach ($classParents as $parent) {
            try {
                $class = new \ReflectionClass($parent);
            } catch (\ReflectionException) {
                continue;
            }

            $ann = $this->reader->firstClassMetadata($class, Entity::class);
            if ($ann !== null) {
                $parents[] = $class;
            }
        }

        return $parents;
    }

    public function tableName(string $role, int $namingStrategy = Entities::TABLE_NAMING_PLURAL): string
    {
        return match ($namingStrategy) {
            Entities::TABLE_NAMING_PLURAL => $this->inflector->pluralize($this->inflector->tableize($role)),
            Entities::TABLE_NAMING_SINGULAR => $this->inflector->singularize($this->inflector->tableize($role)),
            default => $this->inflector->tableize($role),
        };
    }

    public function resolveTarget(Registry $registry, string $name): ?string
    {
        if (\interface_exists($name, true)) {
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
}
