<?php

declare(strict_types=1);

namespace Cycle\Annotated\Annotation\Relation;

use Cycle\Annotated\Annotation\Relation\Traits\InverseTrait;
use Doctrine\Common\Annotations\Annotation\Attribute;
use Doctrine\Common\Annotations\Annotation\Attributes;
use Doctrine\Common\Annotations\Annotation\Enum;

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
 *      @Attribute("though", type="string"),
 *      @Attribute("thoughInnerKey", type="string"),
 *      @Attribute("thoughOuterKey", type="string"),
 *      @Attribute("thoughWhere", type="array"),
 *      @Attribute("through", type="string"),
 *      @Attribute("throughInnerKey", type="array<string>"),
 *      @Attribute("throughOuterKey", type="array<string>"),
 *      @Attribute("throughWhere", type="array"),
 *      @Attribute("fkCreate", type="bool"),
 *      @Attribute("fkAction", type="string"),
 *      @Attribute("indexCreate", type="bool"),
 *      @Attribute("load", type="string"),
 * })
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class ManyToMany extends Relation
{
    use InverseTrait;

    protected const TYPE = 'manyToMany';

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

    /**
     * @var string
     * @deprecated
     */
    protected $though;

    /**
     * @var string
     * @deprecated
     */
    protected $thoughInnerKey;

    /**
     * @var string
     * @deprecated
     */
    protected $thoughOuterKey;

    /**
     * @var array
     * @deprecated
     */
    protected $thoughWhere;

    /** @var string */
    protected $through;

    /** @var array */
    protected $throughInnerKey;

    /** @var array */
    protected $throughOuterKey;

    /** @var array */
    protected $throughWhere;

    /** @var bool */
    protected $fkCreate;

    /**
     * @Enum({"NO ACTION", "CASCADE", "SET NULL"})
     * @var string
     */
    protected $fkAction;

    /** @var bool */
    protected $indexCreate;
}
