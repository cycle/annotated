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
final class HasMany extends Relation
{
    protected const OPTIONS = [
        'cascade'     => Parser::BOOL,
        'nullable'    => Parser::BOOL,
        'innerKey'    => Parser::STRING,
        'outerKey'    => Parser::STRING,
        'where'       => [Parser::MIXED],
        'fkCreate'    => Parser::BOOL,
        'fkAction'    => Parser::STRING,
        'indexCreate' => Parser::BOOL,
    ];
}