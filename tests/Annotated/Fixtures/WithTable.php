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
use Cycle\Annotated\Annotation\Relation\ManyToMany;
use Cycle\Annotated\Annotation\Relation\Morphed\MorphedHasMany;
use Cycle\Annotated\Annotation\Table;
use Cycle\Annotated\Annotation\Table\Index;

/**
 * @Entity()
 * @Table(
 *      columns = {
 *          "name":   @Column(type="string"),
 *          "status": @Column(type="enum(active,disabled)", default="active")
 *      },
 *      indexes = {
 *          @Index(columns={"status"}),
 *          @Index(columns={"name"}, unique=true, name="name_index")
 *      }
 * )
 */
class WithTable implements LabelledInterface
{
    /** @Column(type="primary") */
    protected $id;

    /** @ManyToMany(target="Tag", though="Tag/Context", where={"id": {">=": "1"}}, orderBy={"id": "DESC"}) */
    protected $tags;

    /** @MorphedHasMany(target="Label", outerKey="owner_id", morphKey="owner_role", indexCreate=false) */
    protected $labels;
}
