<?php

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
 * Set up primary key definition from Table annotations.
 */
final class MergePrimaryKey implements GeneratorInterface
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
        }

        return $registry;
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
            /** @var Table\PrimaryKey $primaryKey */
            $primaryKey = $tableMeta->getPrimary() ?? $this->reader->firstClassMetadata($class, Table\PrimaryKey::class);
        } catch (\Exception $e) {
            throw new AnnotationException($e->getMessage(), $e->getCode(), $e);
        }

        if ($primaryKey === null) {
            return;
        }

        $this->renderPrimaryKey($table, $entity, $primaryKey);
    }

    /**
     * @param AbstractTable $table
     * @param Entity        $entity
     * @param Table\Index[] $indexes
     */
    public function renderPrimaryKey(AbstractTable $table, Entity $entity, Table\PrimaryKey $primaryKey): void
    {
        if ($primaryKey->getColumns() === []) {
            throw new AnnotationException(
                "Primary key definition must have columns set on `{$entity->getClass()}`"
            );
        }

        $columns = $this->mapColumns($entity, $primaryKey->getColumns());

        $table->setPrimaryKeys($columns);
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
            [$column,] = AbstractIndex::parseColumn($expression);

            if ($entity->getFields()->has($column)) {
                $mappedName = $entity->getFields()->get($column)->getColumn();
            } else {
                $mappedName = $column;
            }

            $result[] = $mappedName;
        }

        return $result;
    }
}
