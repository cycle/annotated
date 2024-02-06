<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Fixtures\Fixtures25;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Generated;

#[Entity(role: 'generatedFieldsInt', table: 'generated_fields_int')]
class WithGeneratedInt
{
    #[Column(type: 'primary')]
    public int $id;

    #[
        Column(type: 'datetime', name: 'created_at'),
        Generated(type: 2)
    ]
    public \DateTimeImmutable $createdAt;

    #[
        Column(type: 'datetime', name: 'updated_at'),
        Generated(type: 2 | 4)
    ]
    public \DateTimeImmutable $updatedAt;
}
