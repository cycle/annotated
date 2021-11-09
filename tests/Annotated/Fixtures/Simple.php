<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Fixtures;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Relation\HasMany;
use Cycle\Annotated\Annotation\Relation\HasOne;
use Cycle\Annotated\Annotation\Relation\Morphed\MorphedHasMany;
use Cycle\Annotated\Annotation\Relation\RefersTo;

/**
 * @Entity(role="simple")
 */
#[Entity]
class Simple implements LabelledInterface
{
    /**
     * @Column(type="primary", default="xx")
     *
     * @var int
     */
    #[Column(type: 'primary', default: 'xx')]
    protected $id;

    /**
     * @HasOne(target="Complete")
     *
     * @var Complete
     */
    #[HasOne(target: 'Complete')]
    protected $one;

    /**
     * @HasMany(target="WithTable", where={"id": {">=": 1}}, orderBy={"id": "DESC"}, collection="Cycle\Annotated\Tests\Fixtures\Collection\BaseCollection")
     *
     * @var Collection\BaseCollection|WithTable[]
     */
    #[HasMany(target: 'WithTable', where: ['id' => ['>=' => 1]], orderBy: ['id' => 'DESC'], collection: Collection\BaseCollection::class)]
    protected $many;

    /**
     * @RefersTo(target="Simple", fkAction="NO ACTION")
     */
    #[RefersTo(target: 'Simple', fkAction: 'NO ACTION')]
    protected $parent;

    /**
     * @MorphedHasMany(target="Label", outerKey="owner_id", morphKey="owner_role", indexCreate=false, collection="Doctrine\Common\Collections\Collection")
     */
    #[MorphedHasMany(target: 'Label', outerKey: 'owner_id', morphKey: 'owner_role', indexCreate: false, collection: \Doctrine\Common\Collections\Collection::class)]
    protected $labels;
}
