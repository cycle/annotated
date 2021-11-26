<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Functional\Driver\SQLite\Relation;

// phpcs:ignore
use Cycle\Annotated\Tests\Functional\Driver\Common\Relation\HasOneTest as CommonClass;

/**
 * @group driver
 * @group driver-sqlite
 */
class HasOneTest extends CommonClass
{
    public const DRIVER = 'sqlite';
}
