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
     * @param Parser|null $parser
     */
    public function __construct(Parser $parser = null)
    {
        $this->parser = $parser ?? Generator::defaultParser();;
        $this->generator = new Generator($this->parser);
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

            $this->copy($e, $e->getClass());

            // copy from related classes
            $this->copy($e, $e->getMapper());
            $this->copy($e, $e->getRepository());
            $this->copy($e, $e->getSource());
            $this->copy($e, $e->getConstrain());

            foreach ($registry->getChildren($e) as $child) {
                $this->copy($e, $child->getClass());
            }
        }

        return $registry;
    }

    /**
     * @param EntitySchema $e
     * @param string|null  $class
     */
    protected function copy(EntitySchema $e, ?string $class)
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

        // additional columns (mapped to local fields automatically)
        $this->generator->initColumns($e, $ta->getColumns(), $class);
    }
}