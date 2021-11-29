<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Functional\Driver\SQLServer\Inheritance;

// phpcs:ignore
use Cycle\Annotated\Tests\Functional\Driver\Common\Inheritance\SingleTableTest as CommonClass;

/**
 * @group driver
 * @group driver-sqlserver
 */
class SingleTableTest extends CommonClass
{
    public const DRIVER = 'sqlserver';
}
