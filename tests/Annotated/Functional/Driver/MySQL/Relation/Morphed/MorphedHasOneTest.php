<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Functional\Driver\MySQL\Relation\Morphed;

// phpcs:ignore
use Cycle\Annotated\Tests\Functional\Driver\Common\Relation\Morphed\MorphedHasOneTest as CommonClass;

/**
 * @group driver
 * @group driver-mysql
 */
class MorphedHasOneTest extends CommonClass
{
    public const DRIVER = 'mysql';
}
