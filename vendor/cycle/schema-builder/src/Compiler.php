<?php

/**
 * Cycle ORM Schema Builder.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

declare(strict_types=1);

namespace Cycle\Schema;

use Cycle\ORM\Schema;
use Cycle\Schema\Definition\Entity;
use Doctrine\Common\Inflector\Inflector;
use Spiral\Database\Exception\CompilerException;

final class Compiler
{
    /** @var array */
    private $result = [];

    /**
     * Compile the registry schema.
     *
     * @param Registry             $registry
     * @param GeneratorInterface[] $generators
     * @return array
     *
     * @throws CompilerException
     */
    public function compile(Registry $registry, array $generators = []): array
    {
        foreach ($generators as $generator) {
            if (!$generator instanceof GeneratorInterface) {
                throw new CompilerException(sprintf(
                    'Invalid generator `%s`',
                    is_object($generator) ? get_class($generator) : gettype($generator)
                ));
            }

            $registry = $generator->run($registry);
        }

        foreach ($registry->getIterator() as $entity) {
            if ($this->getPrimary($entity) === null) {
                // incomplete entity, skip
                continue;
            }

            $this->compute($registry, $entity);
        }

        return $this->result;
    }

    /**
     * Get compiled schema result.
     *
     * @return array
     */
    public function getSchema(): array
    {
        return $this->result;
    }

    /**
     * Compile entity and relation definitions into packed ORM schema.
     *
     * @param Registry $registry
     * @param Entity   $entity
     */
    protected function compute(Registry $registry, Entity $entity): void
    {
        $schema = [
            Schema::ENTITY       => $entity->getClass(),
            Schema::SOURCE       => $entity->getSource(),
            Schema::MAPPER       => $entity->getMapper(),
            Schema::REPOSITORY   => $entity->getRepository(),
            Schema::CONSTRAIN    => $entity->getConstrain(),
            Schema::SCHEMA       => $entity->getSchema(),
            Schema::PRIMARY_KEY  => $this->getPrimary($entity),
            Schema::COLUMNS      => $this->renderColumns($entity),
            Schema::FIND_BY_KEYS => $this->renderReferences($entity),
            Schema::TYPECAST     => $this->renderTypecast($entity),
            Schema::RELATIONS    => $this->renderRelations($registry, $entity)
        ];

        if ($registry->hasTable($entity)) {
            $schema[Schema::DATABASE] = $registry->getDatabase($entity);
            $schema[Schema::TABLE] = $registry->getTable($entity);
        }

        // table inheritance
        foreach ($registry->getChildren($entity) as $child) {
            $this->result[$child->getClass()] = [Schema::ROLE => $entity->getRole()];
            $schema[Schema::CHILDREN][$this->childAlias($child)] = $child->getClass();
        }

        ksort($schema);
        $this->result[$entity->getRole()] = $schema;
    }

    /**
     * @param Entity $entity
     * @return array
     */
    protected function renderColumns(Entity $entity): array
    {
        $schema = [];
        foreach ($entity->getFields() as $name => $field) {
            $schema[$name] = $field->getColumn();
        }

        return $schema;
    }

    /**
     * @param Entity $entity
     * @return array
     */
    protected function renderTypecast(Entity $entity): array
    {
        $schema = [];
        foreach ($entity->getFields() as $name => $field) {
            if ($field->hasTypecast()) {
                $schema[$name] = $field->getTypecast();
            }
        }

        return $schema;
    }

    /**
     * @param Entity $entity
     * @return array
     */
    protected function renderReferences(Entity $entity): array
    {
        $schema = [$this->getPrimary($entity)];

        foreach ($entity->getFields() as $name => $field) {
            if ($field->isReferenced()) {
                $schema[] = $name;
            }
        }

        return array_unique($schema);
    }

    /**
     * @param Registry $registry
     * @param Entity   $entity
     * @return array
     */
    protected function renderRelations(Registry $registry, Entity $entity): array
    {
        $result = [];
        foreach ($registry->getRelations($entity) as $name => $relation) {
            $result[$name] = $relation->packSchema();
        }

        return $result;
    }

    /**
     * @param Entity $entity
     * @return string|null
     */
    protected function getPrimary(Entity $entity): ?string
    {
        foreach ($entity->getFields() as $name => $field) {
            if ($field->isPrimary()) {
                return $name;
            }
        }

        return null;
    }

    /**
     * Return the unique alias for the child entity.
     *
     * @param Entity $entity
     * @return string
     */
    protected function childAlias(Entity $entity): string
    {
        $r = new \ReflectionClass($entity->getClass());
        return Inflector::classify($r->getShortName());
    }
}
