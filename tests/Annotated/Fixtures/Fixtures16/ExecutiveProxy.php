<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Fixtures\Fixtures16;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;

/**
 * This proxy class doesn't have an {@see Entity} annotation (attribute) declaration,
 * and it shouldn't be presented in Schema.
 * Note: this behavior might be improved. There will be added support for
 * annotated base class columns without Entity annotation declaration.
 */
class ExecutiveProxy extends Employee
{
    /** @Column(type="string") */
    #[Column(type: 'string', name: 'proxy')]
    public ?string $proxyFieldWithAnnotation = null;

    protected int $proxyField;
}
