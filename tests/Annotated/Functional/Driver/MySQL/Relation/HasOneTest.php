<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Functional\Driver\MySQL\Relation;

// phpcs:ignore
use Cycle\Annotated\Tests\Functional\Driver\Common\Relation\HasOneTest as CommonClass;

/**
 * @group driver
 * @group driver-mysql
 */
class HasOneTest extends CommonClass
{
    public const DRIVER = 'mysql';
}
