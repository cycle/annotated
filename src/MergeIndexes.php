<?php

/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

declare(strict_types=1);

namespace Cycle\Annotated;

use Cycle\Annotated\Annotation\Table;
use Cycle\Annotated\Exception\AnnotationException;
use Cycle\Schema\Definition\Entity;
use Cycle\Schema\GeneratorInterface;
use Cycle\Schema\Registry;
use Doctrine\Common\Annotations\AnnotationException as DoctrineException;
use Doctrine\Common\Annotations\AnnotationReader;
use Spiral\Database\Schema\AbstractIndex;
use Spiral\Database\Schema\AbstractTable;

/**
 * Copy index definitions from Mapper/Repository to Entity.
 */
final class MergeIndexes implements GeneratorInterface
{
    /** @var AnnotationReader */
    private $reader;

    /**
     * @param AnnotationReader|null $reader
     */
    public function __construct(AnnotationReader $reader = null)
    {
        $this->reader = $reader ?? new AnnotationReader();
    }

    /**
     * @param Registry $registry
     * @return Registry
     */
    public function run(Registry $registry): Registry
    {
        foreach ($registry as $e) {
            if ($e->getClass() === null || !$registry->hasTable($e)) {
                continue;
            }

            $this->render($registry->getTableSchema($e), $e, $e->getClass());

            // copy table declarations from related classes
            $this->render($registry->getTableSchema($e), $e, $e->getMapper());
            $this->render($registry->getTableSchema($e), $e, $e->getRepository());
            $this->render($registry->getTableSchema($e), $e, $e->getSource());
            $this->render($registry->getTableSchema($e), $e, $e->getConstrain());

            foreach ($registry->getChildren($e) as $child) {
                $this->render($registry->getTableSchema($e), $e, $child->getClass());
            }
        }

        return $registry;
    }

    /**
     * @param AbstractTable $table
     * @param Entity        $entity
     * @param Table\Index[] $indexes
     */
    public function renderIndexes(AbstractTable $table, Entity $entity, array $indexes): void
    {
        foreach ($indexes as $index) {
            if ($index->getColumns() === []) {
                continue;
            }

            $columns = $this->mapColumns($entity, $index->getColumns());

            $indexSchema = $table->index($columns);
            $indexSchema->unique($index->isUnique());

            if ($index->getIndex() !== null) {
                $indexSchema->setName($index->getIndex());
            }
        }
    }

    /**
     * @param AbstractTable $table
     * @param Entity        $entity
     * @param string|null   $class
     */
    protected function render(AbstractTable $table, Entity $entity, ?string $class): void
    {
        if ($class === null) {
            return;
        }

        try {
            $class = new \ReflectionClass($class);
        } catch (\ReflectionException $e) {
            return;
        }

        try {
            $tann = $this->reader->getClassAnnotation($class, Table::class);
        } catch (DoctrineException $e) {
            throw new AnnotationException($e->getMessage(), $e->getCode(), $e);
        }

        if ($tann === null) {
            return;
        }

        $this->renderIndexes($table, $entity, $tann->getIndexes());
    }

    /**
     * @param Entity $entity
     * @param array  $columns
     * @return array
     */
    protected function mapColumns(Entity $entity, array $columns): array
    {
        $result = [];

        foreach ($columns as $expression) {
            [$column, $order] = AbstractIndex::parseColumn($expression);

            if ($entity->getFields()->has($column)) {
                $mappedName = $entity->getFields()->get($column)->getColumn();
            } else {
                $mappedName = $column;
            }

            // Re-construct `column ORDER` with mapped column name
            $result[] = $order ? "$mappedName $order" : $mappedName;
        }

        return $result;
    }
}
