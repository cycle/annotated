<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Functional\Driver\Postgres;

// phpcs:ignore
use Cycle\Annotated\Tests\Functional\Driver\Common\SchemaModifiersTest as CommonClass;

/**
 * @group driver
 * @group driver-postgres
 */
class SchemaModifiersTest extends CommonClass
{
    public const DRIVER = 'postgres';
}
