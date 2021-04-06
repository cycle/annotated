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
    /**
     * @param object<ReaderInterface|DoctrineReader>|null $reader
     *
     * @return ReaderInterface
     *
     * @psalm-type ReaderType = ReaderInterface | DoctrineAnnotationReader | null
     * @psalm-param ReaderType $reader
     */
    public static function create(object $reader = null): ReaderInterface
    {
        switch (true) {
            case $reader instanceof ReaderInterface:
                return $reader;

            case $reader instanceof DoctrineReader:
                return new AnnotationReader($reader);

            case $reader === null:
                return new SelectiveReader([
                    new AttributeReader(),
                    new AnnotationReader(),
                ]);

            default:
                throw new \InvalidArgumentException(
                    sprintf(
                        'Argument $reader must be an instance of %s or %s, but %s passed.',
                        ReaderInterface::class,
                        DoctrineReader::class,
                        'instance of ' . explode("\0", get_class($reader))[0]
                    )
                );
        }
    }
}
