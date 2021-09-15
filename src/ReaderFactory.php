<?php

declare(strict_types=1);

namespace Cycle\Annotated;

use Doctrine\Common\Annotations\Reader as DoctrineReader;
use Spiral\Attributes\AnnotationReader;
use Spiral\Attributes\AttributeReader;
use Spiral\Attributes\Composite\SelectiveReader;
use Spiral\Attributes\ReaderInterface;

final class ReaderFactory
{
    public static function create(DoctrineReader|ReaderInterface $reader = null): ReaderInterface
    {
        return match (true) {
            $reader instanceof ReaderInterface => $reader,
            $reader instanceof DoctrineReader => new AnnotationReader($reader),
            $reader === null => new SelectiveReader([
                new AttributeReader(),
                new AnnotationReader(),
            ]),
        };
    }
}
