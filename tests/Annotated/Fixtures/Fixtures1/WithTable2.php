<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Fixtures\Fixtures1;

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
 * @Index(columns={"name"}, unique=true, name="name_index2")
 */
#[Entity]
#[Column(name: 'name', type: 'string')]
#[Column(name: 'status', property: 'status_property', type: 'enum(active,disabled)', default: 'active')]
#[Column(property: 'no_column_name', type: 'string', default: '')]
#[Index(columns: ['status'])]
#[Index(name: 'name_index2', columns: ['name'], unique: true)]
class WithTable2 implements LabelledInterface
{
    /** @Column(type="primary") */
    #[Column(type: 'primary')]
    protected $id;

    /** @ManyToMany(target="Tag", though="Tag/Context", thoughInnerKey="withTable2_id", thoughOuterKey="tag_id", where={"id": {">=": "1"}}, orderBy={"id": "DESC"}) */
    #[ManyToMany(target: 'Tag', though: 'Tag/Context', thoughInnerKey: 'withTable2_id', thoughOuterKey: 'tag_id', where: ['id' => ['>=' => '1']], orderBy: ['id' => 'DESC'])] // phpcs:ignore
    protected $tags;

    /** @MorphedHasMany(target="Label", outerKey="owner_id", morphKey="owner_role", indexCreate=false) */
    #[MorphedHasMany(target: 'Label', outerKey: 'owner_id', morphKey: 'owner_role', indexCreate: false)]
    protected $labels;
}
