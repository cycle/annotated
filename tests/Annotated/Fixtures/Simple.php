<?php declare(strict_types=1);
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

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
class Simple implements LabelledInterface
{
    /**
     * @Column(type="primary", default="xx")
     * @var int
     */
    protected $id;

    /**
     * @HasOne(target="Complete")
     * @var Complete
     */
    protected $one;

    /**
     * @HasMany(target="WithTable", where={"id": {">": 1}})
     * @var WithTable[]|Collection
     */
    protected $many;

    /**
     * @RefersTo(target="Simple", fkAction="NO ACTION")
     */
    protected $parent;

    /**
     * @MorphedHasMany(target="Label", outerKey="owner_id", morphKey="owner_role", indexCreate=false)
     */
    protected $labels;
}