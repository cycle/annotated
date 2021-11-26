<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Fixtures\Fixtures16;

use Cycle\Annotated\Annotation\Column;

trait ExtraColumns
{
    /** @Column(type="string") */
    #[Column(type: 'string')]
    protected string $hidden;
}
