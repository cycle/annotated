<?php

/**
 * Spiral Framework, SpiralScout LLC.
 *
 * @author    Anton Titov (Wolfy-J)
 */

declare(strict_types=1);

use Doctrine\Common\Annotations\AnnotationRegistry;

error_reporting(E_ALL | E_STRICT);
ini_set('display_errors', '1');

//Composer
require dirname(__DIR__) . '/vendor/autoload.php';

AnnotationRegistry::registerLoader('class_exists');

\Cycle\Annotated\Tests\BaseTest::$config = [
    'debug'     => true,
    'strict'    => true,
    'benchmark' => false,
    'sqlite'    => [
        'driver' => \Spiral\Database\Driver\SQLite\SQLiteDriver::class,
        'check'  => function () {
            return !in_array('sqlite', \PDO::getAvailableDrivers());
        },
        'conn'   => 'sqlite::memory:',
        'user'   => 'sqlite',
        'pass'   => ''
    ],
    'mysql'     => [
        'driver' => \Spiral\Database\Driver\MySQL\MySQLDriver::class,
        'check'  => function () {
            return !in_array('mysql', \PDO::getAvailableDrivers());
        },
        'conn'   => 'mysql:host=127.0.0.1:13306;dbname=spiral',
        'user'   => 'root',
        'pass'   => 'root'
    ],
    'mysql56'   => [
        'driver' => \Spiral\Database\Driver\MySQL\MySQLDriver::class,
        'check'  => function () {
            return !in_array('mysql', \PDO::getAvailableDrivers());
        },
        'conn'   => 'mysql:host=127.0.0.1:13305;dbname=spiral',
        'user'   => 'root',
        'pass'   => 'root'
    ],
    'postgres'  => [
        'driver' => \Spiral\Database\Driver\Postgres\PostgresDriver::class,
        'check'  => function () {
            return !in_array('pgsql', \PDO::getAvailableDrivers());
        },
        'conn'   => 'pgsql:host=127.0.0.1;port=15432;dbname=spiral',
        'user'   => 'postgres',
        'pass'   => 'postgres'
    ],
    'sqlserver' => [
        'driver' => \Spiral\Database\Driver\SQLServer\SQLServerDriver::class,
        'check'  => function () {
            return !in_array('sqlsrv', \PDO::getAvailableDrivers());
        },
        'conn'   => 'sqlsrv:Server=127.0.0.1,11433;Database=tempdb',
        'user'   => 'sa',
        'pass'   => 'SSpaSS__1'
    ],
];

if (!empty(getenv('DB'))) {
    switch (getenv('DB')) {
        case 'postgres':
            \Cycle\Annotated\Tests\BaseTest::$config = [
                'debug'    => false,
                'postgres' => [
                    'driver' => \Spiral\Database\Driver\Postgres\PostgresDriver::class,
                    'check'  => function () {
                        return true;
                    },
                    'conn'   => 'pgsql:host=127.0.0.1;port=5432;dbname=spiral',
                    'user'   => 'postgres',
                    'pass'   => ''
                ],
            ];
            break;

        case 'mysql':
            \Cycle\Annotated\Tests\BaseTest::$config = [
                'debug' => false,
                'mysql' => [
                    'driver' => \Spiral\Database\Driver\MySQL\MySQLDriver::class,
                    'check'  => function () {
                        return true;
                    },
                    'conn'   => 'mysql:host=127.0.0.1:3306;dbname=spiral',
                    'user'   => 'root',
                    'pass'   => 'root'
                ],
            ];
            break;

        case 'mariadb':
            \Cycle\Annotated\Tests\BaseTest::$config = [
                'debug' => false,
                'mysql' => [
                    'driver' => \Spiral\Database\Driver\MySQL\MySQLDriver::class,
                    'check'  => function () {
                        return true;
                    },
                    'conn'   => 'mysql:host=127.0.0.1:3306;dbname=spiral',
                    'user'   => 'root',
                    'pass'   => ''
                ],
            ];
            break;
    }
}
