<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Functional\Driver\SQLite;

// phpcs:ignore
use Cycle\Annotated\Tests\Functional\Driver\Common\SchemaModifiersTest as CommonClass;

/**
 * @group driver
 * @group driver-sqlite
 */
class SchemaModifiersTest extends CommonClass
{
    public const DRIVER = 'sqlite';
}
