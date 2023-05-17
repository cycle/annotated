<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Functional\Driver\SQLite\Relation;

// phpcs:ignore
use Cycle\Annotated\Tests\Functional\Driver\Common\Relation\HasOneTestCase;
use PHPUnit\Framework\Attributes\Group;

#[Group('driver')]
#[Group('driver-sqlite')]
final class HasOneTest extends HasOneTestCase
{
    public const DRIVER = 'sqlite';
}
