<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Functional\Driver\Common;

use Cycle\Annotated\Tests\Fixtures\Fixtures1\TestLogger;
use Cycle\ORM\Config\RelationConfig;
use Cycle\ORM\Factory;
use Cycle\ORM\ORM;
use Cycle\ORM\Schema;
use PHPUnit\Framework\TestCase;
use Spiral\Attributes\AnnotationReader;
use Spiral\Attributes\AttributeReader;
use Spiral\Attributes\Composite\SelectiveReader;
use Cycle\Database\Config\DatabaseConfig;
use Cycle\Database\Database;
use Cycle\Database\DatabaseManager;
use Cycle\Database\Driver\Driver;
use Cycle\Database\Driver\Handler;
use Spiral\Tokenizer\ClassesInterface;
use Spiral\Tokenizer\Config\TokenizerConfig;
use Spiral\Tokenizer\Tokenizer;

abstract class BaseTestCase extends TestCase
{
    // currently active driver
    public const DRIVER = null;
    // tests configuration
    public static array $config;

    // cross test driver cache
    public static array $driverCache = [];

    protected Driver $driver;
    protected DatabaseManager $dbal;
    protected ORM $orm;
    protected TestLogger $logger;
    protected ClassesInterface $locator;

    /**
     * Init all we need.
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->dbal = new DatabaseManager(new DatabaseConfig(['default' => 'default']));
        $this->dbal->addDatabase(new Database(
            'default',
            '',
            $this->getDriver()
        ));

        $this->dbal->addDatabase(new Database(
            'secondary',
            'secondary_',
            $this->getDriver()
        ));

        $this->logger = new TestLogger();
        $this->getDriver()->setLogger($this->logger);

        if (self::$config['debug']) {
            $this->logger->display();
        }

        $this->logger = new TestLogger();
        $this->getDriver()->setLogger($this->logger);

        if (self::$config['debug']) {
            $this->logger->display();
        }

        $this->orm = new ORM(new Factory(
            $this->dbal,
            RelationConfig::getDefault()
        ), new Schema([]));

        $tokenizer = new Tokenizer(new TokenizerConfig([
            'directories' => [__DIR__ . '/../../../Fixtures/Fixtures1'],
            'exclude' => [],
        ]));

        $this->locator = $tokenizer->classLocator();
    }

    /**
     * Cleanup.
     */
    public function tearDown(): void
    {
        $this->disableProfiling();
        $this->dropDatabase($this->dbal->database('default'));
    }

    /**
     * @return Driver
     */
    public function getDriver(): Driver
    {
        if (isset(static::$driverCache[static::DRIVER])) {
            return static::$driverCache[static::DRIVER];
        }

        $config = self::$config[static::DRIVER];
        if (!isset($this->driver)) {
            $this->driver = $config->driver::create($config);
        }

        $this->driver->setProfiling(true);

        return static::$driverCache[static::DRIVER] = $this->driver;
    }

    public static function singularReadersProvider(): \Traversable
    {
        yield ['Annotation reader' => new AnnotationReader()];
        yield ['Attribute reader' => new AttributeReader()];
    }

    public static function allReadersProvider(): \Traversable
    {
        yield from static::singularReadersProvider();
        yield ['Selective reader' => new SelectiveReader([new AttributeReader(), new AnnotationReader()])];
    }

    protected function getDatabase(): Database
    {
        return $this->dbal->database('default');
    }

    protected function dropDatabase(Database $database = null): void
    {
        if (empty($database)) {
            return;
        }

        foreach ($database->getTables() as $table) {
            $schema = $table->getSchema();

            foreach ($schema->getForeignKeys() as $foreign) {
                $schema->dropForeignKey($foreign->getColumns());
            }

            $schema->save(Handler::DROP_FOREIGN_KEYS);
        }

        foreach ($database->getTables() as $table) {
            $schema = $table->getSchema();
            $schema->declareDropped();
            $schema->save();
        }
    }

    /**
     * For debug purposes only.
     */
    protected function enableProfiling(): void
    {
        $this->logger->display();
    }

    /**
     * For debug purposes only.
     */
    protected function disableProfiling(): void
    {
        $this->logger->hide();
    }
}
