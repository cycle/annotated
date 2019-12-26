<?php

/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

declare(strict_types=1);

namespace Cycle\Annotated\Annotation\Relation;

use Doctrine\Common\Annotations\Annotation\Attribute;
use Doctrine\Common\Annotations\Annotation\Attributes;

/**
 * @Annotation
 * @Target("PROPERTY")
 * @Attributes({
 *      @Attribute("target", type="string", required=true),
 *      @Attribute("load", type="string"),
 * })
 */
final class Embedded extends Relation
{
    protected const TYPE = 'embedded';

    /**
     * @return Inverse|null
     */
    public function getInverse(): ?Inverse
    {
        return null;
    }
}
