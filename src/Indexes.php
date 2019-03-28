<?php declare(strict_types=1);
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Cycle\Annotated;

use Cycle\Annotated\Annotation\Table;
use Cycle\Schema\GeneratorInterface;
use Cycle\Schema\Registry;
use Spiral\Annotations\Parser;
use Spiral\Database\Schema\AbstractTable;

final class Indexes implements GeneratorInterface
{
    /** @var Parser */
    private $parser;

    /**
     * @param Parser $parser
     */
    public function __construct(Parser $parser)
    {
        $this->parser = $parser;
    }

    /**
     * @param Registry $registry
     * @return Registry
     */
    public function run(Registry $registry): Registry
    {
        foreach ($registry as $e) {
            if ($e->getClass() === null) {
                continue;
            }

            $this->render(
                $registry->getTableSchema($e),
                new \ReflectionClass($e->getClass())
            );

            foreach ($registry->getChildren($e) as $child) {
                $this->render($registry->getTableSchema($e), new \ReflectionClass($child->getClass()));
            }
        }

        return $registry;
    }

    /**
     * @param AbstractTable    $table
     * @param \ReflectionClass $class
     */
    protected function render(AbstractTable $table, \ReflectionClass $class)
    {
        if ($class->getDocComment() === false) {
            return;
        }

        $ann = $this->parser->parse($class->getDocComment());
        if (!isset($ann[Table::NAME])) {
            return;
        }

        /** @var Table $ta */
        $ta = $ann[Table::NAME];

        $this->renderIndexes($table, $ta->getIndexes());
    }

    /**
     * @param AbstractTable $table
     * @param Table\Index[] $indexes
     */
    public function renderIndexes(AbstractTable $table, array $indexes)
    {
        foreach ($indexes as $index) {
            if ($index->getColumns() === []) {
                continue;
            }

            $indexSchema = $table->index($index->getColumns());
            $indexSchema->unique($index->isUnique());

            if ($index->getIndex() !== null) {
                $indexSchema->setName($index->getIndex());
            }
        }
    }
}