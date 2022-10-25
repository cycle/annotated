<?php

declare(strict_types=1);

namespace Cycle\Annotated\Locator;

use Cycle\Annotated\Annotation\Embeddable;
use Cycle\Annotated\Exception\AnnotationException;
use Cycle\Annotated\ReaderFactory;
use Doctrine\Common\Annotations\Reader as DoctrineReader;
use Spiral\Attributes\ReaderInterface;
use Spiral\Tokenizer\ClassesInterface;

final class TokenizerEmbeddingLocator implements EmbeddingLocatorInterface
{
    /**
     * @var Embedding[]
     */
    private array $embeddings = [];
    private ReaderInterface $reader;

    public function __construct(
        private ClassesInterface $classes,
        DoctrineReader|ReaderInterface $reader = null,
    ) {
        $this->reader = ReaderFactory::create($reader);
    }

    public function getEmbeddings(): array
    {
        $this->embeddings = [];

        foreach ($this->classes->getClasses() as $class) {
            try {
                $attribute = $this->reader->firstClassMetadata($class, Embeddable::class);
            } catch (\Exception $e) {
                throw new AnnotationException($e->getMessage(), $e->getCode(), $e);
            }

            if ($attribute !== null) {
                $this->embeddings[] = new Embedding($attribute, $class);
            }
        }

        return $this->embeddings;
    }
}
