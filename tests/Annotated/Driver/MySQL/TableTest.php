<?php

/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Driver\MySQL;

class TableTest extends \Cycle\Annotated\Tests\TableTest
{
    public const DRIVER = 'mysql';

    public function testOrderedIndexes(): void
    {
        if (getenv('DB') === 'mariadb') {
            $this->expectExceptionMessageRegExp('/column sorting is not supported$/');
        }

        parent::testOrderedIndexes();
    }
}
