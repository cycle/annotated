<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Fixtures\Fixtures19;

enum BackedEnumWrapper: string
{
    case Foo = 'foo';
    case Bar = 'bar';

    public static function typecast(mixed $value): self
    {
        return self::tryFrom((string)$value);
    }
}
