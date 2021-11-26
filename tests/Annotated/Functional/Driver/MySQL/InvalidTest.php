<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Functional\Driver\MySQL;

// phpcs:ignore
use Cycle\Annotated\Tests\Functional\Driver\Common\InvalidTest as CommonClass;

/**
 * @group driver
 * @group driver-mysql
 */
class InvalidTest extends CommonClass
{
    public const DRIVER = 'mysql';
}
