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
use Spiral\Annotations\Parser;
use Spiral\Tokenizer\ClassesInterface;

/**
 * Generates ORM schema based on annotated classes.
 */
class Generator implements GeneratorInterface
{
    /** @var ClassesInterface */
    private $locator;

    /** @var Parser */
    private $parser;

    /**
     * @param ClassesInterface $locator
     * @param Parser           $parser
     */
    public function __construct(ClassesInterface $locator, Parser $parser)
    {
        $this->locator = $locator;
        $this->parser = $parser;
    }

    /**
     * @param Registry $registry
     * @return Registry
     */
    public function run(Registry $registry): Registry
    {
        foreach ($this->locator->getClasses() as $reflection) {
            if ($reflection->getDocComment() === false) {
                continue;
            }

            $head = $this->parser->parse($reflection->getDocComment());

            if (!isset($head[Entity::NAME])) {
                continue;
            }

            // register entity
            dump($head);
        }

        return $registry;
    }
}