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
 *      @Attribute("innerKey", type="array<string>"),
 *      @Attribute("outerKey", type="array<string>"),
 *      @Attribute("fkCreate", type="bool"),
 *      @Attribute("fkAction", type="string"),
 *      @Attribute("fkOnDelete", type="string"),
 *      @Attribute("indexCreate", type="bool"),
 *      @Attribute("load", type="string"),
 *      @Attribute("inverse", type="Cycle\Annotated\Annotation\Relation\Inverse"),
 * })
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class HasOne extends Relation
{
    use InverseTrait;

    protected const TYPE = 'hasOne';

    /** @var bool */
    protected $cascade;

    /** @var bool */
    protected $nullable;

    protected array|string $innerKey;

    protected array|string $outerKey;

    /** @var bool */
    protected $fkCreate;

    /**
     * @Enum({"NO ACTION", "CASCADE", "SET NULL"})
     *
     * @var string
     */
    protected ?string $fkAction = null;

    protected ?bool $indexCreate = null;
}
