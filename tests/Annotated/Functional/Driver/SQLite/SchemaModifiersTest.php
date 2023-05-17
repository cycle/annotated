<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Functional\Driver\SQLite;

// phpcs:ignore
use Cycle\Annotated\Tests\Functional\Driver\Common\SchemaModifiersTestCase;
use PHPUnit\Framework\Attributes\Group;

#[Group('driver')]
#[Group('driver-sqlite')]
final class SchemaModifiersTest extends SchemaModifiersTestCase
{
    public const DRIVER = 'sqlite';
}
