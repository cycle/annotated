<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Fixtures\Locator;

use Spiral\Attributes\Reader;

/**
 * Fails to read the metadata of any property.
 */
final class FailingPropertyReader extends Reader
{
    public function __construct(
        private readonly \Throwable $error,
    ) {}

    public function getClassMetadata(\ReflectionClass $class, ?string $name = null): iterable
    {
        return [];
    }

    public function getFunctionMetadata(\ReflectionFunctionAbstract $function, ?string $name = null): iterable
    {
        return [];
    }

    public function getPropertyMetadata(\ReflectionProperty $property, ?string $name = null): iterable
    {
        throw $this->error;
    }

    public function getConstantMetadata(\ReflectionClassConstant $constant, ?string $name = null): iterable
    {
        return [];
    }

    public function getParameterMetadata(\ReflectionParameter $parameter, ?string $name = null): iterable
    {
        return [];
    }
}
