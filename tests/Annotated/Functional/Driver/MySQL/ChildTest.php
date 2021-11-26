<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Functional\Driver\MySQL;

// phpcs:ignore
use Cycle\Annotated\Tests\Functional\Driver\Common\ChildTest as CommonClass;

/**
 * @group driver
 * @group driver-mysql
 */
class ChildTest extends CommonClass
{
    public const DRIVER = 'mysql';
}
