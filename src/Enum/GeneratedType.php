<?php

declare(strict_types=1);

namespace Cycle\Annotated\Enum;

enum GeneratedType: int
{
    case Db = 1;
    case PhpInsert = 2;
    case PhpUpdate = 4;
}
