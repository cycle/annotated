<?php

declare(strict_types=1);

use Spiral\Tokenizer;

error_reporting(E_ALL | E_STRICT);
ini_set('display_errors', '1');


//Composer
require dirname(__DIR__) . '/vendor/autoload.php';

$tokenizer = new Tokenizer\Tokenizer(new Tokenizer\Config\TokenizerConfig([
    'directories' => [__DIR__ . '/Annotated/Functional/Driver/Common'],
    'exclude' => [],
]));

$databases = [
    'sqlite' => [
        'namespace' => 'Cycle\Annotated\Tests\Functional\Driver\SQLite',
        'directory' => __DIR__ . '/Annotated/Functional/Driver/SQLite/',
    ],
    'mysql' => [
        'namespace' => 'Cycle\Annotated\Tests\Functional\Driver\MySQL',
        'directory' => __DIR__ . '/Annotated/Functional/Driver/MySQL/',
    ],
    'postgres' => [
        'namespace' => 'Cycle\Annotated\Tests\Functional\Driver\Postgres',
        'directory' => __DIR__ . '/Annotated/Functional/Driver/Postgres/',
    ],
    'sqlserver' => [
        'namespace' => 'Cycle\Annotated\Tests\Functional\Driver\SQLServer',
        'directory' => __DIR__ . '/Annotated/Functional/Driver/SQLServer/',
    ],
];

echo "Generating test classes for all database types...\n";

$classes = $tokenizer->classLocator()->getClasses(\Cycle\Annotated\Tests\Functional\Driver\Common\BaseTestCase::class);

foreach ($classes as $baseClass) {
    foreach ($baseClass->getMethods() as $method) {
        if ($method->isAbstract()) {
            echo "Skip class {$baseClass->getName()} with abstract methods.\n";
            continue 2;
        }
    }

    if (
        !$baseClass->isAbstract()
        // Has abstract methods
        || $baseClass->getName() == \Cycle\Annotated\Tests\Functional\Driver\Common\BaseTestCase::class
    ) {
        continue;
    }

    echo "Found {$baseClass->getName()}\n";

    $path = str_replace(
        [str_replace('\\', '/', __DIR__), 'Annotated/Functional/Driver/Common/'],
        '',
        str_replace('\\', '/', \str_replace('TestCase', 'Test', $baseClass->getFileName())),
    );

    $path = ltrim($path, '/');

    foreach ($databases as $driver => $details) {
        $filename = sprintf('%s%s', $details['directory'], $path);
        $dir = pathinfo($filename, PATHINFO_DIRNAME);

        $namespace = str_replace(
            'Cycle\\Annotated\\Tests\\Functional\\Driver\\Common',
            $details['namespace'],
            $baseClass->getNamespaceName(),
        );

        if (!is_dir($dir)) {
            mkdir($dir, recursive: true);
        }

        file_put_contents(
            $filename,
            sprintf(
                <<<PHP
<?php

declare(strict_types=1);

namespace %s;

// phpcs:ignore
use %s;
use PHPUnit\Framework\Attributes\Group;

#[Group('driver')]
#[Group('driver-%s')]
final class %s extends %s
{
    public const DRIVER = '%s';
}

PHP,
                $namespace,
                $baseClass->getName(),
                $driver,
                \str_replace('TestCase', 'Test', $baseClass->getShortName()),
                $baseClass->getShortName(),
                $driver,
            ),
        );
    }
}
