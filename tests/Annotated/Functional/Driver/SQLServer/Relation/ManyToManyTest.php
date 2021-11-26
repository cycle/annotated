<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Functional\Driver\SQLServer\Relation;

// phpcs:ignore
use Cycle\Annotated\Tests\Functional\Driver\Common\Relation\ManyToManyTest as CommonClass;

/**
 * @group driver
 * @group driver-sqlserver
 */
class ManyToManyTest extends CommonClass
{
    public const DRIVER = 'sqlserver';
}
