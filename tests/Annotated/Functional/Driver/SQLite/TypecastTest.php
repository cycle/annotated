<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Functional\Driver\SQLite;

// phpcs:ignore
use Cycle\Annotated\Tests\Functional\Driver\Common\TypecastTest as CommonClass;

/**
 * @group driver
 * @group driver-sqlite
 */
class TypecastTest extends CommonClass
{
    public const DRIVER = 'sqlite';
}
