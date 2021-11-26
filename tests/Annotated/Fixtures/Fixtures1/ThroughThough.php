<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Fixtures\Fixtures1;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Relation\ManyToMany;

/**
 * @Entity()
 * @Column(name="name", type="string"),
 */
#[Entity]
#[Column(name: 'name', type: 'string')]
class ThroughThough
{
    /** @Column(type="primary") */
    #[Column(type: 'primary')]
    protected $id;

    /** @ManyToMany(target="Tag", through="Label", though="Tag\Context", throughInnerKey={"withTable_id"}, throughOuterKey="tag_id") */
    #[ManyToMany(target: 'Tag', through: 'Label', though: "Tag\Context", throughInnerKey: 'withTable_id', throughOuterKey: ['tag_id'])] // phpcs:ignore
    protected $tags;
}
