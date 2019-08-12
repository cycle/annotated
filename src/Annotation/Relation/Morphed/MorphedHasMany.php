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
use Spiral\Annotations\Parser;

/**
 * @Annotation
 * @Target("PROPERTY")
 */
final class MorphedHasMany extends Relation
{
    protected const NAME    = 'morphedHasMany';
    protected const OPTIONS = [
        'cascade'        => Parser::BOOL,
        'nullable'       => Parser::BOOL,
        'innerKey'       => Parser::STRING,
        'outerKey'       => Parser::STRING,
        'morphKey'       => Parser::STRING,
        'morphKeyLength' => Parser::INTEGER,
        'where'          => [Parser::MIXED],
        'indexCreate'    => Parser::BOOL,
    ];
}