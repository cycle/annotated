<?php

declare(strict_types=1);

namespace Cycle\Annotated\Utils;

use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Entities;
use Cycle\Annotated\Exception\AnnotationException;
use Doctrine\Inflector\Inflector;
use Doctrine\Inflector\Rules\English\InflectorFactory;
use Spiral\Attributes\ReaderInterface;

/**
 * @internal
 */
final class EntityUtils
{
    private readonly Inflector $inflector;

    public function __construct(
        private readonly ReaderInterface $reader,
    ) {
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
        $parents = $this->findParents($class);
        $parents = $root ? \array_reverse($parents) : $parents;

        return isset($parents[0]) ? $parents[0]->getName() : null;
    }

    /**
     * @param class-string $class
     *
     * @return class-string
     */
    public function getParent(string $class, bool $root = true): string
    {
        $parent = $this->findParent($class, $root);

        if ($parent === null) {
            throw new AnnotationException(\sprintf('The parent class could not be found for the class `%s`.', $class));
        }

        return $parent;
    }

    /**
     * @param class-string $class
     *
     * @return \ReflectionClass[]
     */
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
}
