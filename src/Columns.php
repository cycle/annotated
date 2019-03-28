<?php declare(strict_types=1);
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Cycle\Annotated;

use Cycle\Annotated\Annotation\Table;
use Cycle\Schema\Definition\Entity as EntitySchema;
use Cycle\Schema\GeneratorInterface;
use Cycle\Schema\Registry;
use Spiral\Annotations\Parser;

final class Columns implements GeneratorInterface
{
    /** @var Parser */
    private $parser;

    /** @var Generator */
    private $generator;

    /**
     * @param Parser $parser
     */
    public function __construct(Parser $parser)
    {
        $this->parser = $parser;
        $this->generator = new Generator($parser);
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

            $this->render($e, new \ReflectionClass($e->getClass()));

            foreach ($registry->getChildren($e) as $child) {
                $this->render($e, new \ReflectionClass($child->getClass()));
            }
        }

        return $registry;
    }

    /**
     * @param EntitySchema     $e
     * @param \ReflectionClass $class
     */
    protected function render(EntitySchema $e, \ReflectionClass $class)
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

        // additional columns (mapped to local fields automatically)
        $this->generator->initColumns($e, $ta->getColumns());
    }
}