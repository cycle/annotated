<?php

/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Fixtures;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Relation\HasMany;
use Cycle\Annotated\Annotation\Relation\HasOne;
use Cycle\Annotated\Annotation\Relation\Morphed\MorphedHasMany;
use Cycle\Annotated\Annotation\Relation\RefersTo;
use Doctrine\Common\Collections\Collection;

/**
 * @Entity(role="simple")
 */
#[Entity]
class Simple implements LabelledInterface
{
    /**
     * @Column(type="primary", default="xx")
     * @var int
     */
    #[Column(type: 'primary', default: 'xx')]
    protected $id;

    /**
     * @HasOne(target="Complete")
     * @var Complete
     */
    #[HasOne(target: "Complete")]
    protected $one;

    /**
     * @HasMany(target="WithTable", where={"id": {">=": 1}}, orderBy={"id": "DESC"})
     * @var WithTable[]|Collection
     */
    #[HasMany(target: "WithTable", where: ["id" => [">=" => 1]], orderBy: ["id" => "DESC"])]
    protected $many;

    /**
     * @RefersTo(target="Simple", fkAction="NO ACTION")
     */
     #[RefersTo(target: "Simple", fkAction: "NO ACTION")]
    protected $parent;

    /**
     * @MorphedHasMany(target="Label", outerKey="owner_id", morphKey="owner_role", indexCreate=false)
     */
     #[MorphedHasMany(target: "Label", outerKey: "owner_id", morphKey: "owner_role", indexCreate: false)]
    protected $labels;
}
