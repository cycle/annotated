<?php

declare(strict_types=1);

namespace Cycle\Annotated\Exception;

class AnnotationWrongTypeArgumentException extends AnnotationException
{
    public static function createFor(\ReflectionProperty $property, \Throwable $e): static
    {
        return new static(
            sprintf(
                'Some of arguments has a wrong type on `%s.%s.`. Error: `%s`',
                $property->getDeclaringClass()->getName(),
                $property->getName(),
                $e->getMessage(),
            ),
            (int) $e->getCode(),
            $e,
        );
    }
}
