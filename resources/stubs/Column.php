<?php

declare(strict_types=1);

namespace Cycle\Annotated\Annotation;

use JetBrains\PhpStorm\ExpectedValues;

final class Column
{
    public function __construct(
        /* @see \Spiral\Database\Schema\AbstractColumn::$mapping */
        #[ExpectedValues(values: ['primary', 'bigPrimary', 'enum', 'boolean', 'integer', 'tinyInteger', 'bigInteger',
            'string', 'text', 'tinyText', 'longText', 'double', 'float', 'decimal', 'datetime', 'date', 'time',
            'timestamp', 'binary', 'tinyBinary', 'longBinary', 'json',
        ])]
        string $type,
        string $name = null,
        bool $primary = false,
        bool $nullable = false,
        mixed $default = null,
        mixed $typecast = null,
    ) {
    }
}
