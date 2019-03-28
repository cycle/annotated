<?php declare(strict_types=1);
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Cycle\Annotated\Annotation;

use Spiral\Annotations\AbstractAnnotation;
use Spiral\Annotations\Parser;

class Entity extends AbstractAnnotation
{
    public const NAME = 'entity';

    public const SCHEMA = [
        'role' => Parser::STRING
    ];
}