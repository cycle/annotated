<?php

declare(strict_types=1);

namespace Cycle\Annotated\Annotation\Relation\Morphed;

use Cycle\Annotated\Annotation\Relation\Relation;
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
 *      @Attribute("innerKey", type="array<string>"),
 *      @Attribute("outerKey", type="array<string>"),
 *      @Attribute("morphKey", type="string"),
 *      @Attribute("morphKeyLength", type="int"),
 *      @Attribute("where", type="array"),
 *      @Attribute("indexCreate", type="bool"),
 *      @Attribute("load", type="string"),
 *      @Attribute("inverse", type="Cycle\Annotated\Annotation\Relation\Inverse"),
 *      @Attribute("collection", type="string"),
 * })
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class MorphedHasMany extends Relation
{
    use InverseTrait;

    protected const TYPE = 'morphedHasMany';

    /** @var bool */
    protected $cascade;

    /** @var bool */
    protected $nullable;

    /** @var string */
    protected $innerKey;

    /** @var string */
    protected $outerKey;

    /** @var string */
    protected $morphKey;

    /** @var int */
    protected $morphKeyLength;

    /** @var array */
    protected $where;

    /** @var bool */
    protected $indexCreate;

    protected ?string $collection = null;
}
