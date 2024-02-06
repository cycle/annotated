<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Fixtures\Fixtures25;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Generated;
use Cycle\Annotated\Enum\GeneratedType;

#[Entity(role: 'generatedFieldsEnum', table: 'generated_fields_enum')]
class WithGeneratedEnum
{
    #[Column(type: 'primary')]
    public int $id;

    #[
        Column(type: 'datetime', name: 'created_at'),
        Generated(type: GeneratedType::PhpInsert)
    ]
    public \DateTimeImmutable $createdAt;

    #[
        Column(type: 'datetime', name: 'updated_at'),
        Generated(GeneratedType::PhpInsert, GeneratedType::PhpUpdate)
    ]
    public \DateTimeImmutable $updatedAt;
}
