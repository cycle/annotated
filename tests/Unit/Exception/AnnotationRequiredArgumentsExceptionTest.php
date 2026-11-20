<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Unit\Exception;

use Cycle\Annotated\Annotation\Relation\Inverse;
use Cycle\Annotated\Exception\AnnotationRequiredArgumentsException;
use Cycle\Annotated\Tests\Fixtures\Configurator\Address;
use Testo\Assert;
use Testo\Test;

#[Test]
final class AnnotationRequiredArgumentsExceptionTest
{
    public function listsTheRequiredArgumentsOfTheAnnotation(): void
    {
        $previous = new \ArgumentCountError('too few', 3);

        $exception = AnnotationRequiredArgumentsException::createFor(
            new \ReflectionProperty(Address::class, 'street'),
            Inverse::class,
            $previous,
        );

        Assert::same(
            $exception->getMessage(),
            \sprintf('Some of required arguments [`as`, `type`] is missed on `%s.street.`', Address::class),
        );
        Assert::same($exception->getCode(), 3);
        Assert::same($exception->getPrevious(), $previous);
    }

    public function acceptsAnAnnotationWithoutConstructor(): void
    {
        $exception = AnnotationRequiredArgumentsException::createFor(
            new \ReflectionProperty(Address::class, 'street'),
            \stdClass::class,
            new \ArgumentCountError(),
        );

        Assert::same(
            $exception->getMessage(),
            \sprintf('Some of required arguments [``] is missed on `%s.street.`', Address::class),
        );
    }
}
