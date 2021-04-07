<?php

declare(strict_types=1);

namespace Cycle\Annotated\Annotation;

use Doctrine\Common\Annotations\Annotation\Attribute;
use Doctrine\Common\Annotations\Annotation\Attributes;
use Doctrine\Common\Annotations\Annotation\Target;

final class Embeddable
{
    public function __construct(
        string $role,
        string $mapper,
        string $columnPrefix = '',
        /** @var array<Column> */
        array $columns = [],
    ) {
    }
}
