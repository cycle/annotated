<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Fixtures\Fixtures1;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Relation\BelongsTo;

/**
 * @Entity(
 *      role       = "eComplete",
 *      mapper     = "CompleteMapper",
 *      repository = "Repository/CompleteRepository",
 *      source     = "Source\TestSource",
 *      constrain  = "Constrain\SomeConstrain",
 *      database   = "secondary",
 *      table      = "complete_data"
 * )
 */
#[Entity(role: 'eComplete', mapper: CompleteMapper::class, repository: 'Repository/CompleteRepository', source: "Source\TestSource", constrain: "Constrain\SomeConstrain", database: 'secondary', table: 'complete_data')] // phpcs:ignore
class Complete implements LabelledInterface
{
    /** @Column(type="primary") */
    #[Column(type: 'primary')]
    protected $id;

    /** @Column(type="string", name="username") */
    #[Column(type: 'string', name: 'username')]
    protected $name;

    /** @var string */
    protected $ignored;

    /** @BelongsTo(target="Simple") */
    #[BelongsTo(target: 'Simple')]
    protected $parent;
}
