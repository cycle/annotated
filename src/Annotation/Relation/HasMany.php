<?php

declare(strict_types=1);

namespace Cycle\Annotated\Annotation\Relation;

use Cycle\Annotated\Annotation\Relation\Traits\InverseTrait;
use Doctrine\Common\Annotations\Annotation\Attribute;
use Doctrine\Common\Annotations\Annotation\Attributes;

/**
 * @Annotation
 * @Target("PROPERTY")
 * @Attributes({
 *      @Attribute("target", type="string", required=true),
 *      @Attribute("cascade", type="bool"),
 *      @Attribute("nullable", type="bool"),
 *      @Attribute("innerKey", type="string"),
 *      @Attribute("outerKey", type="string"),
 *      @Attribute("where", type="array"),
 *      @Attribute("orderBy", type="array"),
 *      @Attribute("fkCreate", type="bool"),
 *      @Attribute("fkAction", type="string"),
 *      @Attribute("indexCreate", type="bool"),
 *      @Attribute("load", type="string"),
 *      @Attribute("inverse", type="Cycle\Annotated\Annotation\Relation\Inverse"),
 * })
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class HasMany extends Relation
{
    use InverseTrait;

    protected const TYPE = 'hasMany';

    /** @var bool */
    protected $cascade;

    /** @var bool */
    protected $nullable;

    /** @var string */
    protected $innerKey;

    /** @var string */
    protected $outerKey;

    /** @var array */
    protected $where;

    /** @var array */
    protected $orderBy;

    /** @var bool */
    protected $fkCreate;

    /**
     * @Enum({"NO ACTION", "CASCADE", "SET NULL"})
     *
     * @var string
     */
    protected $fkAction;

    /** @var bool */
    protected $indexCreate;
}
