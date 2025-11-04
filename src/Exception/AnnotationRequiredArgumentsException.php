<?php

declare(strict_types=1);

namespace Cycle\Annotated\Exception;

class AnnotationRequiredArgumentsException extends AnnotationException
{
    /**
     * @param class-string $annotationClass
     */
    public static function createFor(\ReflectionProperty $property, string $annotationClass, \Throwable $e): static
    {
        $column = new \ReflectionClass($annotationClass);

        $requiredArguments = [];
        foreach ($column->getConstructor()?->getParameters() ?? [] as $parameter) {
            if (!$parameter->isOptional()) {
                $requiredArguments[] = $parameter->getName();
            }
        }

        return new static(
            sprintf(
                'Some of required arguments [`%s`] is missed on `%s.%s.`',
                implode('`, `', $requiredArguments),
                $property->getDeclaringClass()->getName(),
                $property->getName(),
            ),
            (int) $e->getCode(),
            $e,
        );
    }
}
