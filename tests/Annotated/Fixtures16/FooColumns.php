<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Fixtures16;

use Cycle\Annotated\Annotation\Column;

trait FooColumns
{
    /** @Column(type="string") */
    #[Column(type: 'string')]
    protected string $bar;
}
