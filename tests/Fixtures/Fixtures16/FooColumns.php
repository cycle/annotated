<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Fixtures\Fixtures16;

use Cycle\Annotated\Annotation\Column;

trait FooColumns
{
    /** @Column(type="string") */
    #[Column(type: 'string')]
    public string $bar;
}
