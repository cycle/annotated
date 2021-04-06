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
     * @param ReaderInterface|DoctrineReader|null $reader
     *
     * @return ReaderInterface
     *
     * @psalm-type ReaderType = ReaderInterface | DoctrineAnnotationReader | null
     * @psalm-param ReaderType $reader
     */
    public static function create($reader = null): ReaderInterface
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
                        'Reader must be instance of %s. %s given instead.',
                        ReaderInterface::class,
                        is_object($reader) ? 'Instance of ' . get_class($reader) : ucfirst(gettype($reader))
                    )
                );
        }
    }
}
