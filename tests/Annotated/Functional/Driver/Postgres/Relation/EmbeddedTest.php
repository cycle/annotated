<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Functional\Driver\Postgres\Relation;

// phpcs:ignore
use Cycle\Annotated\Tests\Functional\Driver\Common\Relation\EmbeddedTest as CommonClass;

/**
 * @group driver
 * @group driver-postgres
 */
class EmbeddedTest extends CommonClass
{
    public const DRIVER = 'postgres';
}
