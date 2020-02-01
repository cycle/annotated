<?php

/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

declare(strict_types=1);

namespace Cycle\Schema\Relation;

use Cycle\ORM\Relation;
use Cycle\Schema\Registry;
use Cycle\Schema\Relation\Traits\ForeignKeyTrait;

final class Embedded extends RelationSchema
{
    use ForeignKeyTrait;

    // internal relation type
    protected const RELATION_TYPE = Relation::EMBEDDED;

    // relation schema options
    protected const RELATION_SCHEMA = [
        Relation::LOAD => Relation::LOAD_EAGER,
    ];

    /**
     * @param Registry $registry
     */
    public function compute(Registry $registry): void
    {
        $source = $registry->getEntity($this->source);
        $target = $registry->getEntity($this->target);

        // each embedded entity must isolated
        $target = clone $target;
        $target->setRole($source->getRole() . ':' . $target->getRole());

        // embedded entity must point to the same table as parent entity
        $registry->register($target);
        $registry->linkTable($target, $registry->getDatabase($source), $registry->getTable($source));

        // isolated
        $this->target = $target->getRole();

        foreach ($source->getFields() as $name => $field) {
            if ($field->isPrimary()) {
                // sync primary keys
                $target->getFields()->set($name, $field);
            }
        }

        parent::compute($registry);
    }

    /**
     * @param Registry $registry
     */
    public function render(Registry $registry): void
    {
        // relation does not require any column rendering besides actual tables
    }
}
