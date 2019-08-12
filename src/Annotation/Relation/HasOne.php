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
final class HasOne extends Relation
{
    protected const NAME    = 'hasOne';
    protected const OPTIONS = [
        'cascade'     => Parser::BOOL,
        'nullable'    => Parser::BOOL,
        'innerKey'    => Parser::STRING,
        'outerKey'    => Parser::STRING,
        'fkCreate'    => Parser::BOOL,
        'fkAction'    => Parser::STRING,
        'indexCreate' => Parser::BOOL,
    ];
}
