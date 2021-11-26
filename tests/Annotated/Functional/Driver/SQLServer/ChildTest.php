<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Functional\Driver\SQLServer;

// phpcs:ignore
use Cycle\Annotated\Tests\Functional\Driver\Common\ChildTest as CommonClass;

/**
 * @group driver
 * @group driver-sqlserver
 */
class ChildTest extends CommonClass
{
    public const DRIVER = 'sqlserver';
}
