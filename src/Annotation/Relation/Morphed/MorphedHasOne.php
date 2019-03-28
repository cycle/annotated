<?php declare(strict_types=1);
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Cycle\Annotated\Annotation\Relation\Morphed;

use Cycle\Annotated\Annotation\Relation\Relation;
use Spiral\Annotations\Parser;

final class MorphedHasOne extends Relation
{
    protected const NAME    = 'morphedHasOne';
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