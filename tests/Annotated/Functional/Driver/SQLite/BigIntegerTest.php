<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Functional\Driver\SQLite;

// phpcs:ignore
use Cycle\Annotated\Tests\Functional\Driver\Common\BigIntegerTest as CommonClass;

/**
 * @group driver
 * @group driver-sqlite
 */
class BigIntegerTest extends CommonClass
{
    public const DRIVER = 'sqlite';
}
