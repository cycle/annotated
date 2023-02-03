<?php

declare(strict_types=1);

namespace Cycle\Annotated\Utils;

use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Entities;
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
        /** @var class-string[] $parents */
        $parents = class_parents($class);

        $parents = $root ? array_reverse($parents) : $parents;

        foreach ($parents as $parent) {
            try {
                $class = new \ReflectionClass($parent);
            } catch (\ReflectionException) {
                continue;
            }

            $ann = $this->reader->firstClassMetadata($class, Entity::class);
            if ($ann !== null) {
                return $parent;
            }
        }

        return null;
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
