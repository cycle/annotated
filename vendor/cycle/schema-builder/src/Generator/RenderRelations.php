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
use Cycle\Schema\GeneratorInterface;
use Cycle\Schema\Registry;

/**
 * Renders all required relations columns, indexes and foreign keys.
 */
final class RenderRelations implements GeneratorInterface
{
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
     */
    protected function compute(Registry $registry, Entity $entity): void
    {
        foreach ($registry->getRelations($entity) as $relation) {
            $relation->render($registry);
        }
    }
}
