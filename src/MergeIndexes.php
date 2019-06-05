<?php declare(strict_types=1);
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Cycle\Annotated;

use Cycle\Annotated\Annotation\Table;
use Cycle\Schema\Definition\Entity;
use Cycle\Schema\GeneratorInterface;
use Cycle\Schema\Registry;
use Spiral\Annotations\Parser;
use Spiral\Database\Schema\AbstractTable;

final class MergeIndexes implements GeneratorInterface
{
    /** @var Parser */
    private $parser;

    /**
     * @param Parser|null $parser
     */
    public function __construct(Parser $parser = null)
    {
        $this->parser = $parser ?? Generator::getDefaultParser();
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
     * @param string|null   $class
     */
    protected function render(AbstractTable $table, Entity $entity, ?string $class)
    {
        if ($class === null) {
            return;
        }

        try {
            $class = new \ReflectionClass($class);
        } catch (\ReflectionException $e) {
            return;
        }

        if ($class->getDocComment() === false) {
            return;
        }

        $ann = $this->parser->parse($class->getDocComment());
        if (!isset($ann[Table::NAME])) {
            return;
        }

        /** @var Table $ta */
        $ta = $ann[Table::NAME];

        $this->renderIndexes($table, $entity, $ta->getIndexes());
    }

    /**
     * @param AbstractTable $table
     * @param Entity        $entity
     * @param Table\Index[] $indexes
     */
    public function renderIndexes(AbstractTable $table, Entity $entity, array $indexes)
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
     * @param Entity $entity
     * @param array  $columns
     * @return array
     */
    protected function mapColumns(Entity $entity, array $columns): array
    {
        $result = [];

        foreach ($columns as $column) {
            if ($entity->getFields()->has($column)) {
                $result[] = $entity->getFields()->get($column)->getColumn();
            } else {
                $result[] = $column;
            }
        }

        return $result;
    }
}