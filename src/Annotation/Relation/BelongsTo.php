<?php declare(strict_types=1);
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Cycle\Annotated\Annotation\Relation;

use Spiral\Annotations\Parser;

final class BelongsTo extends Relation
{
    protected const NAME    = 'belongsTo';
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