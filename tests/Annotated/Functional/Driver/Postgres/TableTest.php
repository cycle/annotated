<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Functional\Driver\Postgres;

// phpcs:ignore
use Cycle\Annotated\Tests\Functional\Driver\Common\TableTestCase;
use PHPUnit\Framework\Attributes\Group;

#[Group('driver')]
#[Group('driver-postgres')]
final class TableTest extends TableTestCase
{
    public const DRIVER = 'postgres';
}
