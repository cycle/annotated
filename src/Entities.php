<?php declare(strict_types=1);
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Cycle\Annotated;

use Cycle\Annotated\Annotation\Entity;
use Cycle\Schema\GeneratorInterface;
use Cycle\Schema\Registry;
use Doctrine\Common\Inflector\Inflector;
use Spiral\Annotations\Parser;
use Spiral\Tokenizer\ClassesInterface;

/**
 * Generates ORM schema based on annotated classes.
 */
final class Entities implements GeneratorInterface
{
    /** @var ClassesInterface */
    private $locator;

    /** @var Parser */
    private $parser;

    /** @var Generator */
    private $generator;

    /**
     * @param ClassesInterface $locator
     * @param Parser           $parser
     */
    public function __construct(ClassesInterface $locator, Parser $parser)
    {
        $this->locator = $locator;
        $this->parser = $parser;
        $this->generator = new Generator($parser);
    }

    /**
     * @param Registry $registry
     * @return Registry
     */
    public function run(Registry $registry): Registry
    {
        foreach ($this->locator->getClasses() as $class) {
            if ($class->getDocComment() === false) {
                continue;
            }

            $ann = $this->parser->parse($class->getDocComment());
            if (!isset($ann[Entity::NAME])) {
                continue;
            }

            /** @var Entity $ea */
            $ea = $ann[Entity::NAME];

            $e = $this->generator->initEntity($ea, $class);

            // columns
            $this->generator->initFields($e, $class);

            // relations
            $this->generator->initRelations($e, $class);

            // register entity (OR find parent)
            $registry->register($e);

            $registry->linkTable(
                $e,
                $ea->getDatabase(),
                $ea->getTable() ?? $this->tableName($e->getRole())
            );
        }

        return $registry;
    }

    /**
     * @param string $role
     * @return string
     */
    protected function tableName(string $role): string
    {
        return Inflector::pluralize(Inflector::tableize($role));
    }
}