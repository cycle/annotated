<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Functional\Driver\SQLServer;

// phpcs:ignore
use Cycle\Annotated\Tests\Functional\Driver\Common\SchemaModifiersTest as CommonClass;

/**
 * @group driver
 * @group driver-sqlserver
 */
class SchemaModifiersTest extends CommonClass
{
    public const DRIVER = 'sqlserver';
}
