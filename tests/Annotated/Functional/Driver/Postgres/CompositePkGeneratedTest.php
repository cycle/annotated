<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Functional\Driver\Postgres;

use Cycle\Annotated\Tests\Functional\Driver\Common\CompositePkGeneratedTestCase;
use PHPUnit\Framework\Attributes\Group;

#[Group('driver')]
#[Group('driver-postgres')]
final class CompositePkGeneratedTest extends CompositePkGeneratedTestCase
{
    public const DRIVER = 'postgres';
}
