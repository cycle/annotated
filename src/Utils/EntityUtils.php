<?php

declare(strict_types=1);

namespace Cycle\Annotated\Utils;

use Cycle\Annotated\Annotation\Entity;
use Doctrine\Common\Annotations\Reader as DoctrineReader;
use Spiral\Attributes\ReaderInterface;

class EntityUtils
{
    public function __construct(
        private DoctrineReader|ReaderInterface $reader
    ) {
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

            if ($class->getDocComment() === false) {
                continue;
            }

            $ann = $this->reader->firstClassMetadata($class, Entity::class);
            if ($ann !== null) {
                return $parent;
            }
        }

        return null;
    }
}
