<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Functional\Driver\MySQL;

// phpcs:ignore
use Cycle\Annotated\Tests\Functional\Driver\Common\TableTest as CommonClass;
use Spiral\Attributes\ReaderInterface;

/**
 * @group driver
 * @group driver-mysql
 */
class TableTest extends CommonClass
{
    public const DRIVER = 'mysql';

    /**
     * @dataProvider singularReadersProvider
     */
    public function testOrderedIndexes(ReaderInterface $reader): void
    {
        if (getenv('DB') === 'mariadb') {
            $this->expectExceptionMessageMatches('/column sorting is not supported$/');
        }

        parent::testOrderedIndexes($reader);
    }
}
