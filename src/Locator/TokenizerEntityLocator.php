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
    private readonly ReaderInterface $reader;

    public function __construct(
        private readonly ClassesInterface $classes,
        DoctrineReader|ReaderInterface|null $reader = null,
    ) {
        $this->reader = ReaderFactory::create($reader);
    }

    public function getEntities(): array
    {
        $entities = [];
        foreach ($this->classes->getClasses() as $class) {
            try {
                /** @var Attribute $attribute */
                $attribute = $this->reader->firstClassMetadata($class, Attribute::class);
            } catch (\Exception $e) {
                throw new AnnotationException($e->getMessage(), (int) $e->getCode(), $e);
            }

            if ($attribute !== null) {
                $entities[] = new Entity($attribute, $class);
            }
        }

        return $entities;
    }
}
