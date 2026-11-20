<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Fixtures\Fixtures16;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Inheritance\DiscriminatorColumn;
use Cycle\Annotated\Annotation\Relation\HasOne;

/**
 * @Entity
 * @DiscriminatorColumn(name="type")
 */
#[Entity]
#[DiscriminatorColumn(name: 'type')]
class Person
{
    /** @Column(type="string") */
    #[Column(type: 'string')]
    public string $name;

    /** @Column(type="string") */
    #[Column(type: 'string')]
    public string $type;

    /** @Column(type="int", nullable=true, typecast="int") */
    #[Column(type: 'int', nullable: true, typecast: 'int')]
    public ?int $tool_id;

    /** @HasOne(target=Tool::class, innerKey="id", outerKey="tool_id", nullable=true, fkCreate=false) */
    #[HasOne(target: Tool::class, innerKey: 'id', outerKey: 'tool_id', nullable: true, fkCreate: false)]
    public Tool $tool;

    /** @Column(type="primary", name="id") */
    #[Column(type: 'primary', name: 'id')]
    protected int $foo_id;

    public function getFooId(): int
    {
        return $this->foo_id;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
