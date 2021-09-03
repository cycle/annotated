<?php

declare(strict_types=1);

namespace Cycle\Annotated;

use Cycle\Annotated\Annotation\Embeddable;
use Cycle\Annotated\Annotation\Relation\RelationInterface;
use Cycle\Annotated\Exception\AnnotationException;
use Cycle\Schema\Definition\Entity as EntitySchema;
use Cycle\Schema\GeneratorInterface;
use Cycle\Schema\Registry;
use Doctrine\Common\Annotations\Reader as DoctrineReader;
use Spiral\Attributes\ReaderInterface;
use Spiral\Tokenizer\ClassesInterface;

/**
 * Generates ORM schema based on annotated classes.
 */
final class Embeddings implements GeneratorInterface
{
    /** @var ClassesInterface */
    private $locator;

    /** @var ReaderInterface */
    private $reader;

    /** @var Configurator */
    private $generator;

    /**
     * @param ClassesInterface $locator
     * @param object<DoctrineReader|ReaderInterface>|null $reader
     */
    public function __construct(ClassesInterface $locator, object $reader = null)
    {
        $this->locator = $locator;
        $this->reader = ReaderFactory::create($reader);
        $this->generator = new Configurator($this->reader);
    }

    /**
     * @param Registry $registry
     *
     * @return Registry
     */
    public function run(Registry $registry): Registry
    {
        foreach ($this->locator->getClasses() as $class) {
            try {
                /** @var Embeddable $em */
                $em = $this->reader->firstClassMetadata($class, Embeddable::class);
            } catch (\Exception $e) {
                throw new AnnotationException($e->getMessage(), $e->getCode(), $e);
            }
            if ($em === null) {
                continue;
            }

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
    public function verifyNoRelations(EntitySchema $entity, \ReflectionClass $class): void
    {
        foreach ($class->getProperties() as $property) {
            try {
                $ann = $this->reader->getPropertyMetadata($property);
            } catch (\Exception $e) {
                throw new AnnotationException($e->getMessage(), $e->getCode(), $e);
            }

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
