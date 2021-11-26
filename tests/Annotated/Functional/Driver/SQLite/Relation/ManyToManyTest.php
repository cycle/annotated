<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Functional\Driver\SQLite\Relation;

// phpcs:ignore
use Cycle\Annotated\Tests\Functional\Driver\Common\Relation\ManyToManyTest as CommonClass;

/**
 * @group driver
 * @group driver-sqlite
 */
class ManyToManyTest extends CommonClass
{
    public const DRIVER = 'sqlite';
}
