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
 *      @Attribute("fkOnDelete", type="string"),
 *      @Attribute("indexCreate", type="bool"),
 *      @Attribute("load", type="string"),
 *      @Attribute("collection", type="string"),
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

    protected array|string $innerKey;

    protected array|string $outerKey;

    protected array $where = [];

    protected array $orderBy = [];

    protected ?string $collection = null;

    /**
     * @deprecated
     */
    protected ?string $though = null;

    /**
     * @deprecated
     */
    protected array|string $thoughInnerKey;

    /**
     * @deprecated
     */
    protected array|string $thoughOuterKey;

    /**
     * @deprecated
     */
    protected array $thoughWhere = [];

    protected ?string $through = null;

    protected array|string $throughInnerKey;

    protected array|string $throughOuterKey;

    protected array $throughWhere = [];

    /** @var bool */
    protected $fkCreate;

    /**
     * @Enum({"NO ACTION", "CASCADE", "SET NULL"})
     */
    protected ?string $fkAction = null;

    /** @var bool */
    protected $indexCreate;
}
