<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Functional\Driver\MySQL;

// phpcs:ignore
use Cycle\Annotated\Tests\Functional\Driver\Common\GeneratorTestCase;
use PHPUnit\Framework\Attributes\Group;

#[Group('driver')]
#[Group('driver-mysql')]
final class GeneratorTest extends GeneratorTestCase
{
    public const DRIVER = 'mysql';
}
