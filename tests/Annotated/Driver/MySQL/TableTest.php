<?php

/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Driver\MySQL;

use Spiral\Attributes\ReaderInterface;

class TableTest extends \Cycle\Annotated\Tests\TableTest
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
