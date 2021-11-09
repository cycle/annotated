<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Fixtures;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Relation\ManyToMany;
use Cycle\Annotated\Annotation\Relation\Morphed\MorphedHasMany;
use Cycle\Annotated\Annotation\Table\Index;

/**
 * Short syntax
 *
 * @Entity()
 * @Column(name="name", type="string"),
 * @Column(name="status", type="enum(active,disabled)", default="active", property="status_property")
 * @Column(property="no_column_name", type="string", default="")
 * @Index(columns={"status"}),
 * @Index(columns={"name"}, unique=true, name="name_index")
 */
#[Entity]
#[Column(name: 'name', type: 'string')]
#[Column(name: 'status', property: 'status_property', type: 'enum(active,disabled)', default: 'active')]
#[Column(property: 'no_column_name', type: 'string', default: '')]
#[Index(columns: ['status'])]
#[Index(name: 'name_index', columns: ['name'], unique: true)]
class WithTable implements LabelledInterface
{
    /** @Column(type="primary") */
    #[Column(type: 'primary')]
    protected $id;

    /** @ManyToMany(
     *     target="Tag",
     *     through="Tag/Context",
     *     throughInnerKey={"withTable_id"},
     *     throughOuterKey="tag_id",
     *     where={"id": {">=": "1"}},
     *     orderBy={"id": "DESC"},
     *     collection="Cycle\Annotated\Tests\Fixtures\Collection\BaseCollection"
     * )
     */
    #[ManyToMany(target: 'Tag', through: 'Tag/Context', throughInnerKey: 'withTable_id', throughOuterKey: ['tag_id'], where: ['id' => ['>=' => '1']], orderBy: ['id' => 'DESC'], collection: Collection\BaseCollection::class)] // phpcs:ignore
    protected $tags;

    /** @MorphedHasMany(target="Label", outerKey="owner_id", morphKey="owner_role", indexCreate=false) */
    #[MorphedHasMany(target: 'Label', outerKey: 'owner_id', morphKey: 'owner_role', indexCreate: false)]
    protected $labels;
}
