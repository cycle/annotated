<?php

declare(strict_types=1);

namespace Cycle\Annotated\Annotation\Relation\Morphed;

use Cycle\Annotated\Annotation\Relation\Relation;
use Cycle\Annotated\Annotation\Relation\Traits\InverseTrait;
use Doctrine\Common\Annotations\Annotation\Attribute;
use Doctrine\Common\Annotations\Annotation\Attributes;
use Doctrine\Common\Annotations\Annotation\Target;

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
 *      @Attribute("indexCreate", type="bool"),
 *      @Attribute("inverse", type="Cycle\Annotated\Annotation\Relation\Inverse"),
 * })
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class BelongsToMorphed extends Relation
{
    use InverseTrait;

    protected const TYPE = 'belongsToMorphed';

    /** @var bool */
    protected $cascade;

    /** @var bool */
    protected $nullable;

    /** @var string */
    protected $innerKey;

    /** @var string */
    protected $outerKey;

    protected ?string $morphKey = null;

    /** @var int */
    protected $morphKeyLength;

    /** @var bool */
    protected $indexCreate;
}
