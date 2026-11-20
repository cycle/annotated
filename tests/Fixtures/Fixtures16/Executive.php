<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Fixtures\Fixtures16;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Inheritance\JoinedTable as InheritanceJoinedTable;
use Cycle\Annotated\Annotation\Relation\HasOne;

/**
 * @Entity
 * @InheritanceJoinedTable(outerKey="foo_id")
 */
#[Entity]
#[InheritanceJoinedTable(outerKey: 'foo_id')]
class Executive extends ExecutiveProxy
{
    use ExtraColumns;

    /** @Column(type="int") */
    #[Column(type: 'int')]
    public int $bonus;

    /** @Column(type="int", nullable=true, typecast="int") */
    #[Column(type: 'int', nullable: true, typecast: 'int')]
    public ?int $added_tool_id;

    /** @HasOne(target=Tool::class, innerKey="added_tool_id", outerKey="added_tool_id", nullable=true, fkCreate=false) */
    #[HasOne(target: Tool::class, innerKey: 'added_tool_id', outerKey: 'added_tool_id', nullable: true, fkCreate: false)]
    public Tool $addedTool;
}
