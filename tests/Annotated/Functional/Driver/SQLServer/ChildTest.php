<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Functional\Driver\SQLServer;

// phpcs:ignore
use Cycle\Annotated\Tests\Functional\Driver\Common\ChildTestCase;
use PHPUnit\Framework\Attributes\Group;

#[Group('driver')]
#[Group('driver-sqlserver')]
final class ChildTest extends ChildTestCase
{
    public const DRIVER = 'sqlserver';
}
