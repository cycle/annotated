<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */
declare(strict_types=1);

namespace Cycle\Annotated\Annotation\Relation;

use Spiral\Annotations\Parser;

/**
 * @Annotation
 * @Target("PROPERTY")
 */
final class ManyToMany extends Relation
{
    protected const NAME    = 'manyToMany';
    protected const OPTIONS = [
        'cascade'        => Parser::BOOL,
        'nullable'       => Parser::BOOL,
        'innerKey'       => Parser::STRING,
        'outerKey'       => Parser::STRING,
        'though'         => Parser::STRING,
        'thoughInnerKey' => Parser::STRING,
        'thoughOuterKey' => Parser::STRING,
        'thoughWhere'    => [Parser::MIXED],
        'where'          => [Parser::MIXED],
        'fkCreate'       => Parser::BOOL,
        'fkAction'       => Parser::STRING,
        'indexCreate'    => Parser::BOOL,
    ];
}
