<?php

declare(strict_types=1);

namespace Cycle\Annotated\Locator;

use Cycle\Annotated\Annotation\Entity as Attribute;
use Cycle\Annotated\Exception\AnnotationException;
use Cycle\Annotated\ReaderFactory;
use Doctrine\Common\Annotations\Reader as DoctrineReader;
use Spiral\Attributes\ReaderInterface;
use Spiral\Tokenizer\ClassesInterface;

final class TokenizerEntityLocator implements EntityLocatorInterface
{
    /**
     * @var Entity[]
     */
    private array $entities = [];
    private ReaderInterface $reader;

    public function __construct(
        private ClassesInterface $classes,
        DoctrineReader|ReaderInterface $reader = null,
    ) {
        $this->reader = ReaderFactory::create($reader);
    }

    public function getEntities(): array
    {
        $this->entities = [];

        foreach ($this->classes->getClasses() as $class) {
            try {
                /** @var Attribute $attribute */
                $attribute = $this->reader->firstClassMetadata($class, Attribute::class);
            } catch (\Exception $e) {
                throw new AnnotationException($e->getMessage(), $e->getCode(), $e);
            }

            if ($attribute !== null) {
                $this->entities[] = new Entity($attribute, $class);
            }
        }

        return $this->entities;
    }
}
