<?php

declare(strict_types=1);

namespace Cycle\Annotated;

use Cycle\Annotated\Annotation\Relation\RelationInterface;
use Cycle\Annotated\Exception\AnnotationException;
use Cycle\Annotated\Locator\EmbeddingLocatorInterface;
use Cycle\Schema\Definition\Entity as EntitySchema;
use Cycle\Schema\GeneratorInterface;
use Cycle\Schema\Registry;
use Doctrine\Common\Annotations\Reader as DoctrineReader;
use Spiral\Attributes\ReaderInterface;

/**
 * Generates ORM schema based on annotated classes.
 */
final class Embeddings implements GeneratorInterface
{
    private readonly ReaderInterface $reader;
    private readonly Configurator $generator;

    public function __construct(
        private readonly EmbeddingLocatorInterface $locator,
        DoctrineReader|ReaderInterface|null $reader = null,
    ) {
        $this->reader = ReaderFactory::create($reader);
        $this->generator = new Configurator($this->reader);
    }

    public function run(Registry $registry): Registry
    {
        foreach ($this->locator->getEmbeddings() as $embedding) {
            $e = $this->generator->initEmbedding($embedding->attribute, $embedding->class);

            $this->verifyNoRelations($e, $embedding->class);

            // columns
            $this->generator->initFields($e, $embedding->class);

            // register entity (OR find parent)
            $registry->register($e);
        }

        return $registry;
    }

    public function verifyNoRelations(EntitySchema $entity, \ReflectionClass $class): void
    {
        foreach ($class->getProperties() as $property) {
            try {
                /** @var object[] $ann */
                $ann = $this->reader->getPropertyMetadata($property);
            } catch (\Exception $e) {
                throw new AnnotationException($e->getMessage(), (int) $e->getCode(), $e);
            }

            foreach ($ann as $ra) {
                if ($ra instanceof RelationInterface) {
                    throw new AnnotationException(
                        "Relations are not allowed within embeddable entities in `{$entity->getClass()}`",
                    );
                }
            }
        }
    }
}
