<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Functional\Driver\Postgres\Inheritance;

// phpcs:ignore
use Cycle\Annotated\Tests\Functional\Driver\Common\Inheritance\SingleTableTestCase;
use PHPUnit\Framework\Attributes\Group;

#[Group('driver')]
#[Group('driver-postgres')]
final class SingleTableTest extends SingleTableTestCase
{
    public const DRIVER = 'postgres';
}
