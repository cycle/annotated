<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */
declare(strict_types=1);

namespace Cycle\Annotated\Annotation\Relation\Morphed;

use Cycle\Annotated\Annotation\Relation\Relation;
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
 *      @Attribute("innerKey", type="string"),
 *      @Attribute("outerKey", type="string"),
 *      @Attribute("morphKey", type="string"),
 *      @Attribute("morphKeyLength", type="int"),
 *      @Attribute("indexCreate", type="bool"),
 *      @Attribute("load", type="string"),
 * })
 */
final class BelongsToMorphed extends Relation
{

}