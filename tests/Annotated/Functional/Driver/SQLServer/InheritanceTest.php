<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Functional\Driver\SQLServer;

// phpcs:ignore
use Cycle\Annotated\Tests\Functional\Driver\Common\InheritanceTest as CommonClass;

/**
 * @group driver
 * @group driver-sqlserver
 */
class InheritanceTest extends CommonClass
{
    public const DRIVER = 'sqlserver';
}
