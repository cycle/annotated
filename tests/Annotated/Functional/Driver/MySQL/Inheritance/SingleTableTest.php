<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Functional\Driver\MySQL\Inheritance;

// phpcs:ignore
use Cycle\Annotated\Tests\Functional\Driver\Common\Inheritance\SingleTableTest as CommonClass;

/**
 * @group driver
 * @group driver-mysql
 */
class SingleTableTest extends CommonClass
{
    public const DRIVER = 'mysql';
}
