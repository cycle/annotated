<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Fixtures\Fixtures25\Php82;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Generated;
use Cycle\Annotated\Enum\GeneratedType;

#[Entity(role: 'generatedFieldsEnumValue', table: 'generated_fields_enum_value')]
class WithGenerated
{
    #[Column(type: 'primary')]
    public int $id;

    #[
        Column(type: 'datetime', name: 'created_at'),
        Generated(type: GeneratedType::PhpInsert->value)
    ]
    public \DateTimeImmutable $createdAt;

    #[
        Column(type: 'datetime', name: 'updated_at'),
        Generated(type: GeneratedType::PhpInsert->value | GeneratedType::PhpUpdate->value)
    ]
    public \DateTimeImmutable $updatedAt;
}
