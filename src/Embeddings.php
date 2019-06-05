<?php declare(strict_types=1);
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Cycle\Annotated;

use Cycle\Annotated\Annotation\Embeddable;
use Cycle\Annotated\Annotation\Relation\RelationInterface;
use Cycle\Annotated\Exception\AnnotationException;
use Cycle\Schema\Definition\Entity as EntitySchema;
use Cycle\Schema\GeneratorInterface;
use Cycle\Schema\Registry;
use Spiral\Annotations\Parser;
use Spiral\Tokenizer\ClassesInterface;

/**
 * Generates ORM schema based on annotated classes.
 */
final class Embeddings implements GeneratorInterface
{
    /** @var ClassesInterface */
    private $locator;

    /** @var Parser */
    private $parser;

    /** @var Generator */
    private $generator;

    /**
     * @param ClassesInterface $locator
     * @param Parser|null      $parser
     */
    public function __construct(ClassesInterface $locator, Parser $parser = null)
    {
        $this->locator = $locator;
        $this->parser = $parser ?? Generator::getDefaultParser();
        $this->generator = new Generator($this->parser);
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
            if (!isset($ann[Embeddable::NAME])) {
                continue;
            }

            /** @var Embeddable $em */
            $em = $ann[Embeddable::NAME];

            $e = $this->generator->initEmbedding($em, $class);

            $this->verifyNoRelations($e, $class);

            // columns
            $this->generator->initFields($e, $class, $em->getColumnPrefix());

            // register entity (OR find parent)
            $registry->register($e);
        }

        return $registry;
    }

    /**
     * @param EntitySchema     $entity
     * @param \ReflectionClass $class
     */
    public function verifyNoRelations(EntitySchema $entity, \ReflectionClass $class)
    {
        foreach ($class->getProperties() as $property) {
            if ($property->getDocComment() === false) {
                continue;
            }

            $ann = $this->parser->parse($property->getDocComment());

            foreach ($ann as $ra) {
                if ($ra instanceof RelationInterface) {
                    throw new AnnotationException(
                        "Relations are not allowed within embeddable entities in `{$entity->getClass()}`"
                    );
                }
            }
        }
    }
}

