<?php

/**
 * Cycle ORM Schema Builder.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

declare(strict_types=1);

namespace Cycle\Schema\Generator;

use Cycle\Schema\Exception\SyncException;
use Cycle\Schema\GeneratorInterface;
use Cycle\Schema\Registry;
use Spiral\Database\Schema\Reflector;

/**
 * Sync table schemas with database.
 */
final class SyncTables implements GeneratorInterface
{
    // Readonly tables must be included form the sync with database
    public const READONLY_SCHEMA = 'readonlySchema';

    /**
     * @param Registry $registry
     * @return Registry
     *
     * @throws SyncException
     */
    public function run(Registry $registry): Registry
    {
        $reflector = new Reflector();
        foreach ($registry as $entity) {
            if (!$registry->hasTable($entity) || $entity->getOptions()->has(self::READONLY_SCHEMA)) {
                continue;
            }

            $reflector->addTable($registry->getTableSchema($entity));
        }

        try {
            $reflector->run();
        } catch (\Throwable $e) {
            throw new SyncException($e->getMessage(), $e->getCode(), $e);
        }

        return $registry;
    }
}
