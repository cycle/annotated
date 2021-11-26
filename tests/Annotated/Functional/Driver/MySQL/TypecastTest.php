<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Functional\Driver\MySQL;

// phpcs:ignore
use Cycle\Annotated\Tests\Functional\Driver\Common\TypecastTest as CommonClass;

/**
 * @group driver
 * @group driver-mysql
 */
class TypecastTest extends CommonClass
{
    public const DRIVER = 'mysql';
}
