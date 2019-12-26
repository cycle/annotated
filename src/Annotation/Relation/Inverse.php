<?php

/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

declare(strict_types=1);

namespace Cycle\Annotated\Annotation\Relation;

use Cycle\ORM\Relation;
use Doctrine\Common\Annotations\Annotation\Attribute;
use Doctrine\Common\Annotations\Annotation\Attributes;
use Doctrine\Common\Annotations\Annotation\Enum;

/**
 * @Annotation
 * @Target({"PROPERTY", "ANNOTATION"})
 * @Attributes({
 *      @Attribute("as", type="string", required=true),
 *      @Attribute("type", type="string", required=true),
 *      @Attribute("load", type="string"),
 * })
 */
final class Inverse
{
    /** @var string */
    private $as;

    /** @var string */
    private $type;

    /**
     * @Enum({"eager", "lazy", "promise"}
     * @var string
     */
    private $load;

    /**
     * @param array $values
     */
    public function __construct(array $values)
    {
        foreach ($values as $key => $value) {
            if ($key == 'fetch') {
                $key = 'load';
            }

            $this->$key = $value;
        }
    }

    /**
     * @return string|null
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->as;
    }

    /**
     * @return int|null
     */
    public function getLoadMethod(): ?int
    {
        switch ($this->load) {
            case 'eager':
            case Relation::LOAD_EAGER:
                return Relation::LOAD_EAGER;
            case 'promise':
            case 'lazy':
            case Relation::LOAD_PROMISE:
                return Relation::LOAD_PROMISE;
            default:
                return null;
        }
    }
}
