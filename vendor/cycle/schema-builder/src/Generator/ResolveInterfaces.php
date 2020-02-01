<?php

/**
 * Cycle ORM Schema Builder.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

declare(strict_types=1);

namespace Cycle\Schema\Generator;

use Cycle\Schema\Definition\Entity;
use Cycle\Schema\Exception\RegistryException;
use Cycle\Schema\Exception\RelationException;
use Cycle\Schema\GeneratorInterface;
use Cycle\Schema\Registry;

final class ResolveInterfaces implements GeneratorInterface
{
    // option to indicate that static link resolution is required
    public const STATIC_LINK = 'staticLink';

    /**
     * @param Registry $registry
     * @return Registry
     */
    public function run(Registry $registry): Registry
    {
        foreach ($registry as $entity) {
            $this->compute($registry, $entity);
        }

        return $registry;
    }

    /**
     * @param Registry $registry
     * @param Entity   $entity
     *
     * @throws RelationException
     */
    protected function compute(Registry $registry, Entity $entity): void
    {
        foreach ($entity->getRelations() as $relation) {
            if (!$relation->getOptions()->has(self::STATIC_LINK)) {
                continue;
            }

            $target = $relation->getTarget();
            if ($registry->hasEntity($target)) {
                // no need to resolve
                continue;
            }

            $relation->setTarget($this->resolve($registry, $target));
            $relation->getOptions()->remove(self::STATIC_LINK);
        }
    }

    /**
     * @param Registry $registry
     * @param string   $target
     * @return string
     */
    protected function resolve(Registry $registry, string $target): string
    {
        if (!interface_exists($target)) {
            throw new RelationException("Unable to resolve static link to non interface target `{$target}`");
        }

        $found = null;
        foreach ($registry->getIterator() as $entity) {
            if ($entity->getClass() === null) {
                continue;
            }

            try {
                $candidate = new \ReflectionClass($entity->getClass());
            } catch (\ReflectionException $e) {
                throw new RegistryException($e->getMessage(), $e->getCode(), $e);
            }

            if ($candidate->isSubclassOf($target) || $candidate->implementsInterface($target)) {
                if ($found !== null) {
                    throw new RelationException("Ambiguous static link to `{$target}`");
                }

                $found = $entity->getClass();
            }
        }

        if ($found !== null) {
            return $found;
        }

        throw new RelationException("Unable to resolve static link to `{$target}`");
    }
}
