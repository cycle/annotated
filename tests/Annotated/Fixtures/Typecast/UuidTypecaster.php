<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Fixtures\Typecast;

use Cycle\ORM\Parser\TypecastInterface;

class UuidTypecaster implements TypecastInterface
{
    public function cast(array $values): array
    {
        return $values;
    }
}
