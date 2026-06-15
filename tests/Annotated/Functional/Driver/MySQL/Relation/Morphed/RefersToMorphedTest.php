<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Functional\Driver\MySQL\Relation\Morphed;

// phpcs:ignore
use Cycle\Annotated\Tests\Functional\Driver\Common\Relation\Morphed\RefersToMorphedTestCase;
use PHPUnit\Framework\Attributes\Group;

#[Group('driver')]
#[Group('driver-mysql')]
final class RefersToMorphedTest extends RefersToMorphedTestCase
{
    public const DRIVER = 'mysql';
}
