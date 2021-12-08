<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Functional\Driver\MySQL\Inheritance;

// phpcs:ignore
use Cycle\Annotated\Tests\Functional\Driver\Common\Inheritance\JoinedTableTest as CommonClass;

/**
 * @group driver
 * @group driver-mysql
 */
class JoinedTableTest extends CommonClass
{
    public const DRIVER = 'mysql';
}
