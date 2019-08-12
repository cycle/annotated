<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */
declare(strict_types=1);

namespace Cycle\Annotated\Annotation\Relation;

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
}