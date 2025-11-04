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
use Cycle\Database\Schema\AbstractIndex;
use Cycle\Database\Schema\AbstractTable;

/**
 * Copy index definitions from Mapper/Repository to Entity.
 */
final class MergeIndexes implements GeneratorInterface
{
    private readonly ReaderInterface $reader;

    public function __construct(DoctrineReader|ReaderInterface|null $reader = null)
    {
        $this->reader = ReaderFactory::create($reader);
    }

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
            $this->render($registry->getTableSchema($e), $e, $e->getScope());

            foreach ($registry->getChildren($e) as $child) {
                \assert($child->getRole() !== null);
                if (!$child->isChildOfSingleTableInheritance() && $registry->hasEntity($child->getRole())) {
                    $tableSchema = $registry->getTableSchema($child);
                } else {
                    $tableSchema = $registry->getTableSchema($e);
                }
                $this->render($tableSchema, $e, $child->getClass());
            }
        }

        return $registry;
    }

    /**
     * @param Table\Index[] $indexes
     */
    public function renderIndexes(AbstractTable $table, Entity $entity, array $indexes): void
    {
        foreach ($indexes as $index) {
            if ($index->getColumns() === []) {
                throw new AnnotationException(
                    "Invalid index definition for `{$entity->getRole()}`. Column list can't be empty.",
                );
            }

            $columns = $this->mapColumns($entity, $index->getColumns());

            if ($index instanceof Table\PrimaryKey) {
                $table->setPrimaryKeys($columns);
                $entity->setPrimaryColumns($index->getColumns());
            } else {
                $indexSchema = $table->index($columns);
                $indexSchema->unique($index->isUnique());

                if (($idx = $index->getIndex()) !== null) {
                    $indexSchema->setName($idx);
                }
            }
        }
    }

    /**
     * @return string[]
     */
    protected function mapColumns(Entity $entity, array $columns): array
    {
        $result = [];

        foreach ($columns as $expression) {
            [$column, $order] = AbstractIndex::parseColumn($expression);

            $mappedName = $entity->getFields()->has($column)
                ? $entity->getFields()->get($column)->getColumn()
                : $column;

            // Re-construct `column ORDER` with mapped column name
            $result[] = $order ? "$mappedName $order" : $mappedName;
        }

        return $result;
    }

    /**
     * @param class-string|null   $class
     */
    private function render(AbstractTable $table, Entity $entity, ?string $class): void
    {
        if ($class === null) {
            return;
        }

        try {
            $reflection = new \ReflectionClass($class);
        } catch (\ReflectionException $e) {
            return;
        }

        try {
            /** @var Table|null $tableMeta */
            $tableMeta = $this->reader->firstClassMetadata($reflection, Table::class);
            /** @var Table\PrimaryKey|null $primaryKey */
            $primaryKey = $tableMeta
                ? $tableMeta->getPrimary()
                : $this->reader->firstClassMetadata($reflection, Table\PrimaryKey::class);

            /** @var Table\Index[] $indexMeta */
            $indexMeta = $this->reader->getClassMetadata($reflection, Table\Index::class);
        } catch (\Exception $e) {
            throw new AnnotationException($e->getMessage(), (int) $e->getCode(), $e);
        }

        $indexes = $tableMeta === null ? [] : $tableMeta->getIndexes();

        if ($primaryKey !== null) {
            $indexes[] = $primaryKey;
        }

        foreach ($indexMeta as $index) {
            $indexes[] = $index;
        }

        if ($indexes === []) {
            return;
        }

        $this->renderIndexes($table, $entity, $indexes);
    }
}
