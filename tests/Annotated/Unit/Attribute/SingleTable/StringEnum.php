<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Unit\Attribute\SingleTable;

enum StringEnum: string
{
    case A = 'a';
    case B = 'b';
}
