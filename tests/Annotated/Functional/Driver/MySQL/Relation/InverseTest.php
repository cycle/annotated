<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Functional\Driver\MySQL\Relation;

// phpcs:ignore
use Cycle\Annotated\Tests\Functional\Driver\Common\Relation\InverseTest as CommonClass;

/**
 * @group driver
 * @group driver-mysql
 */
class InverseTest extends CommonClass
{
    public const DRIVER = 'mysql';
}
