<?php

declare(strict_types=1);

namespace Cycle\Annotated\Annotation\Table;

use Doctrine\Common\Annotations\Annotation\Attribute;
use Doctrine\Common\Annotations\Annotation\Attributes;
use Doctrine\Common\Annotations\Annotation\Target;

/**
 * @Annotation
 * @Target("ANNOTATION", "CLASS")
 * @Attributes({
 *      @Attribute("columns", type="array<string>", required=true),
 * })
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
class PrimaryKey extends Index
{
    /**
     * @param array<string, mixed> $values
     */
    public function __construct(array $values)
    {
        $values['unique'] = true;
        $values['name'] = 'PK';
        parent::__construct($values);
    }
}
