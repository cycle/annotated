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
use Doctrine\Common\Annotations\Reader as DoctrineReader;
use Spiral\Attributes\ReaderInterface;
use Spiral\Database\Schema\AbstractIndex;
use Spiral\Database\Schema\AbstractTable;

/**
 * Copy index definitions from Mapper/Repository to Entity.
 */
final class MergeIndexes implements GeneratorInterface
{
    /** @var ReaderInterface */
    private $reader;

    /**
     * @param object<ReaderInterface|DoctrineReader>|null $reader
     */
    public function __construct(object $reader = null)
    {
        $this->reader = ReaderFactory::create($reader);
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
            /** @var Table|null $tableMeta */
            $tableMeta = $this->reader->firstClassMetadata($class, Table::class);
            /** @var Table\Index[] $indexMeta */
            $indexMeta = $this->reader->getClassMetadata($class, Table\Index::class);
        } catch (\Exception $e) {
            throw new AnnotationException($e->getMessage(), $e->getCode(), $e);
        }

        $indexes = $tableMeta === null ? [] : $tableMeta->getIndexes();
        foreach ($indexMeta as $index) {
            $indexes[] = $index;
        }

        if ($indexes === []) {
            return;
        }

        $this->renderIndexes($table, $entity, $indexes);
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
