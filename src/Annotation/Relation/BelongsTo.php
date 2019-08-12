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

/**
 * @Annotation
 * @Target("PROPERTY")
 * @Attributes({
 *      @Attribute("target", type="string", required=true),
 *      @Attribute("cascade", type="bool"),
 *      @Attribute("nullable", type="bool"),
 *      @Attribute("innerKey", type="string"),
 *      @Attribute("outerKey", type="string"),
 *      @Attribute("fkCreate", type="bool"),
 *      @Attribute("fkAction", type="string"),
 *      @Attribute("indexCreate", type="bool"),
 * })
 */
final class BelongsTo extends Relation
{
}