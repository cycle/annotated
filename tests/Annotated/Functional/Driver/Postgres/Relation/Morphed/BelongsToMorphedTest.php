<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Functional\Driver\Postgres\Relation\Morphed;

// phpcs:ignore
use Cycle\Annotated\Tests\Functional\Driver\Common\Relation\Morphed\BelongsToMorphedTest as CommonClass;

/**
 * @group driver
 * @group driver-postgres
 */
class BelongsToMorphedTest extends CommonClass
{
    public const DRIVER = 'postgres';
}
