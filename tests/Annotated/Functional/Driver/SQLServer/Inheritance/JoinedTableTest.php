<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Functional\Driver\SQLServer\Inheritance;

// phpcs:ignore
use Cycle\Annotated\Tests\Functional\Driver\Common\Inheritance\JoinedTableTestCase;
use PHPUnit\Framework\Attributes\Group;

#[Group('driver')]
#[Group('driver-sqlserver')]
final class JoinedTableTest extends JoinedTableTestCase
{
    public const DRIVER = 'sqlserver';
}
