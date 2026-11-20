<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Functional\Driver\Common;

use Cycle\Annotated\Entities;
use Cycle\Annotated\Exception\AnnotationException;
use Cycle\Annotated\Locator\TokenizerEntityLocator;
use Cycle\Annotated\MergeColumns;
use Cycle\Annotated\MergeIndexes;
use Cycle\ORM\Schema;
use Cycle\Schema\Compiler;
use Cycle\Schema\Generator\ForeignKeys;
use Cycle\Schema\Generator\RenderTables;
use Cycle\Schema\Generator\SyncTables;
use Cycle\Schema\Registry;
use PHPUnit\Framework\Attributes\DataProvider;
use Spiral\Attributes\AnnotationReader;
use Spiral\Attributes\AttributeReader;
use Spiral\Attributes\Composite\SelectiveReader;
use Spiral\Attributes\ReaderInterface;
use Spiral\Tokenizer\Config\TokenizerConfig;
use Spiral\Tokenizer\Tokenizer;

abstract class TableTestCase extends BaseTestCase
{
    public static function foreignKeyDirectoriesDataProvider(): \Traversable
    {
        yield [new AttributeReader(), '/Fixtures/Fixtures24/Class/DatabaseField'];
        yield [new AttributeReader(), '/Fixtures/Fixtures24/Class/PrimaryKey', 'id'];
        yield [new AttributeReader(), '/Fixtures/Fixtures24/Class/PropertyName'];

        yield [new AnnotationReader(), '/Fixtures/Fixtures24/Class/DatabaseField'];
        yield [new AnnotationReader(), '/Fixtures/Fixtures24/Class/PrimaryKey', 'id'];
        yield [new AnnotationReader(), '/Fixtures/Fixtures24/Class/PropertyName'];

        yield [
            new SelectiveReader([new AnnotationReader(), new AttributeReader()]),
            '/Fixtures/Fixtures24/Class/DatabaseField',
        ];
        yield [
            new SelectiveReader([new AnnotationReader(), new AttributeReader()]),
            '/Fixtures/Fixtures24/Class/PrimaryKey',
            'id',
        ];
        yield [
            new SelectiveReader([new AnnotationReader(), new AttributeReader()]),
            '/Fixtures/Fixtures24/Class/PropertyName',
        ];

        yield [new AnnotationReader(), '/Fixtures/Fixtures24/Entity/DatabaseField'];
        yield [new AnnotationReader(), '/Fixtures/Fixtures24/Entity/PrimaryKey', 'id'];
        yield [new AnnotationReader(), '/Fixtures/Fixtures24/Entity/PropertyName'];

        yield [new AttributeReader(), '/Fixtures/Fixtures24/Property/DatabaseField'];
        yield [new AttributeReader(), '/Fixtures/Fixtures24/Property/PrimaryKey', 'id'];
        yield [new AttributeReader(), '/Fixtures/Fixtures24/Property/PropertyName'];

        yield [new AnnotationReader(), '/Fixtures/Fixtures24/Property/DatabaseField'];
        yield [new AnnotationReader(), '/Fixtures/Fixtures24/Property/PrimaryKey', 'id'];
        yield [new AnnotationReader(), '/Fixtures/Fixtures24/Property/PropertyName'];

        yield [
            new SelectiveReader([new AnnotationReader(), new AttributeReader()]),
            '/Fixtures/Fixtures24/Property/DatabaseField',
        ];
        yield [
            new SelectiveReader([new AnnotationReader(), new AttributeReader()]),
            '/Fixtures/Fixtures24/Property/PrimaryKey',
            'id',
        ];
        yield [
            new SelectiveReader([new AnnotationReader(), new AttributeReader()]),
            '/Fixtures/Fixtures24/Property/PropertyName',
        ];
    }

    #[\Testo\Data\DataProvider('allReadersProvider')]
    #[\Testo\Test]
    public function testColumnsRendered(ReaderInterface $reader): void
    {
        $r = new Registry($this->dbal);
        (new Entities(new TokenizerEntityLocator($this->locator, $reader), $reader))->run($r);
        (new MergeColumns($reader))->run($r);
        (new RenderTables())->run($r);

        \Testo\Assert::true($r->hasTable($r->getEntity('withTable')));

        $schema = $r->getTableSchema($r->getEntity('withTable'));

        \Testo\Assert::true($schema->hasColumn('name'));
        \Testo\Assert::same($schema->column('status')->getType(), 'string');

        \Testo\Assert::true($schema->hasColumn('status'));

        \Testo\Assert::same($schema->column('status')->getAbstractType(), 'enum');
        \Testo\Assert::same($schema->column('status')->getDefaultValue(), 'active');
        \Testo\Assert::same($schema->column('status')->getEnumValues(), ['active', 'disabled']);
    }

    #[\Testo\Data\DataProvider('allReadersProvider')]
    #[\Testo\Test]
    public function testColumnWithDifferentColumnNameAndProperty(ReaderInterface $reader): void
    {
        $r = new Registry($this->dbal);
        $schema = (new Compiler())->compile(
            $r,
            [
                new Entities(new TokenizerEntityLocator($this->locator, $reader), $reader),
                new MergeColumns($reader),
                new RenderTables(),
            ],
        );
        \Testo\Assert::array($schema)->hasKeys('withTable');
        \Testo\Assert::array($schema['withTable'][Schema::COLUMNS])->hasKeys('status_property');
        \Testo\Assert::same($schema['withTable'][Schema::COLUMNS]['status_property'], 'status');
    }

    #[\Testo\Data\DataProvider('allReadersProvider')]
    #[\Testo\Test]
    public function testColumnWithoutColumnName(ReaderInterface $reader): void
    {
        $r = new Registry($this->dbal);
        $schema = (new Compiler())->compile(
            $r,
            [
                new Entities(new TokenizerEntityLocator($this->locator, $reader), $reader),
                new MergeColumns($reader),
                new RenderTables(),
            ],
        );
        \Testo\Assert::array($schema)->hasKeys('withTable');
        \Testo\Assert::array($schema['withTable'][Schema::COLUMNS])->hasKeys('no_column_name');
        \Testo\Assert::same($schema['withTable'][Schema::COLUMNS]['no_column_name'], 'no_column_name');
    }

    #[\Testo\Test]
    public function testColumnInTableAnnotationByNumericKey(): void
    {
        $reader = new AnnotationReader();
        $tokenizer = new Tokenizer(new TokenizerConfig([
            'directories' => [__DIR__ . '/../../../Fixtures/Fixtures9'],
            'exclude' => [],
        ]));
        $locator = $tokenizer->classLocator();
        $r = new Registry($this->dbal);
        $schema = (new Compiler())->compile($r, [
            new Entities(new TokenizerEntityLocator($locator, $reader), $reader),
            new MergeColumns($reader),
            new RenderTables(),
        ]);

        \Testo\Assert::array($schema)->hasKeys('orderedIdx');
        \Testo\Assert::array($schema['orderedIdx'][Schema::COLUMNS])->hasKeys('other_name');
        \Testo\Assert::same($schema['orderedIdx'][Schema::COLUMNS]['other_name'], 'other_name');
    }

    #[\Testo\Test]
    public function testColumnWithPropertyInTableAnnotationByNamedKey(): void
    {
        $reader = new AnnotationReader();
        $tokenizer = new Tokenizer(new TokenizerConfig([
            'directories' => [__DIR__ . '/../../../Fixtures/Fixtures11'],
            'exclude' => [],
        ]));
        $locator = $tokenizer->classLocator();
        $r = new Registry($this->dbal);

        \Testo\Expect::exception(AnnotationException::class)->withMessageContaining('Can not use name "name1" for Column of the `badEntity` role, because the '
        . '"property" field of the metadata class has already been set to "name2".');

        (new Compiler())->compile($r, [
            new Entities(new TokenizerEntityLocator($locator, $reader), $reader),
            new MergeColumns($reader),
            new RenderTables(),
        ]);
    }

    #[\Testo\Data\DataProvider('singularReadersProvider')]
    #[\Testo\Test]
    public function testCompositePrimaryKey(ReaderInterface $reader): void
    {
        $r = new Registry($this->dbal);

        $tokenizer = new Tokenizer(new TokenizerConfig([
            'directories' => [__DIR__ . '/../../../Fixtures/Fixtures12'],
            'exclude' => [],
        ]));
        $locator = $tokenizer->classLocator();

        (new Entities(new TokenizerEntityLocator($locator, $reader), $reader))->run($r);
        (new MergeColumns($reader))->run($r);
        (new RenderTables())->run($r);
        (new MergeIndexes($reader))->run($r);
        (new SyncTables())->run($r);

        $schema = $r->getTableSchema($r->getEntity('compositePost'));

        \Testo\Assert::equals($schema->getPrimaryKeys(), ['id', 'user_id']);
    }

    #[\Testo\Data\DataProvider('singularReadersProvider')]
    #[\Testo\Test]
    public function testIndexWithEmptyColumnsShouldThrowAnException(ReaderInterface $reader): void
    {
        \Testo\Expect::exception(AnnotationException::class)->withMessageContaining('Invalid index definition for `compositePost`. Column list can\'t be empty.');

        $r = new Registry($this->dbal);

        $tokenizer = new Tokenizer(new TokenizerConfig([
            'directories' => [__DIR__ . '/../../../Fixtures/Fixtures13'],
            'exclude' => [],
        ]));
        $locator = $tokenizer->classLocator();

        (new Entities(new TokenizerEntityLocator($locator, $reader), $reader))->run($r);
        (new MergeColumns($reader))->run($r);
        (new RenderTables())->run($r);
        (new MergeIndexes($reader))->run($r);
    }

    #[\Testo\Data\DataProvider('allReadersProvider')]
    #[\Testo\Test]
    public function testIndexes(ReaderInterface $reader): void
    {
        $r = new Registry($this->dbal);

        (new Entities(new TokenizerEntityLocator($this->locator, $reader), $reader))->run($r);
        (new MergeColumns($reader))->run($r);
        (new RenderTables())->run($r);
        (new MergeIndexes($reader))->run($r);
        (new SyncTables())->run($r);

        \Testo\Assert::true($r->hasTable($r->getEntity('withTable')));

        $schema = $r->getTableSchema($r->getEntity('withTable'));

        \Testo\Assert::true($schema->hasIndex(['name']));
        \Testo\Assert::true($schema->index(['name'])->isUnique());
        \Testo\Assert::same($schema->index(['name'])->getName(), 'name_index');

        \Testo\Assert::true($schema->hasIndex(['status']));
    }

    #[\Testo\Data\DataProvider('singularReadersProvider')]
    #[\Testo\Test]
    public function testOrderedIndexes(ReaderInterface $reader): void
    {
        $tokenizer = new Tokenizer(new TokenizerConfig([
            'directories' => [__DIR__ . '/../../../Fixtures/Fixtures9'],
            'exclude' => [],
        ]));

        $locator = $tokenizer->classLocator();

        $r = new Registry($this->dbal);

        (new Entities(new TokenizerEntityLocator($locator, $reader), $reader))->run($r);
        (new MergeColumns($reader))->run($r);
        (new RenderTables())->run($r);
        (new MergeIndexes($reader))->run($r);
        (new SyncTables())->run($r);

        \Testo\Assert::true($r->hasTable($r->getEntity('orderedIdx')));

        $schema = $r->getTableSchema($r->getEntity('orderedIdx'));

        \Testo\Assert::true($schema->hasColumn('name'));
        \Testo\Assert::true($schema->hasIndex(['name', 'id DESC']));
    }

    #[\Testo\Data\DataProvider('allReadersProvider')]
    #[\Testo\Test]
    public function testNamingDefault(ReaderInterface $reader): void
    {
        $r = new Registry($this->dbal);
        (new Entities(new TokenizerEntityLocator($this->locator, $reader), $reader))->run($r);
        (new MergeColumns($reader))->run($r);
        (new RenderTables())->run($r);

        \Testo\Assert::true($r->hasTable($r->getEntity('withTable')));

        $schema = $r->getTableSchema($r->getEntity('withTable'));

        \Testo\Assert::same($schema->getName(), 'with_tables');
    }

    #[\Testo\Data\DataProvider('allReadersProvider')]
    #[\Testo\Test]
    public function testNamingPluralize(ReaderInterface $reader): void
    {
        $r = new Registry($this->dbal);
        (new Entities(
            new TokenizerEntityLocator($this->locator, $reader),
            $reader,
            Entities::TABLE_NAMING_PLURAL,
        ))->run($r);
        (new MergeColumns($reader))->run($r);
        (new RenderTables())->run($r);

        \Testo\Assert::true($r->hasTable($r->getEntity('withTable')));

        $schema = $r->getTableSchema($r->getEntity('withTable'));

        \Testo\Assert::same($schema->getName(), 'with_tables');
    }

    #[\Testo\Data\DataProvider('allReadersProvider')]
    #[\Testo\Test]
    public function testNamingSingular(ReaderInterface $reader): void
    {
        $r = new Registry($this->dbal);
        (new Entities(
            new TokenizerEntityLocator($this->locator, $reader),
            $reader,
            Entities::TABLE_NAMING_SINGULAR,
        ))->run($r);
        (new MergeColumns($reader))->run($r);
        (new RenderTables())->run($r);

        \Testo\Assert::true($r->hasTable($r->getEntity('withTable')));

        $schema = $r->getTableSchema($r->getEntity('withTable'));

        \Testo\Assert::same($schema->getName(), 'with_table');
    }

    #[\Testo\Data\DataProvider('allReadersProvider')]
    #[\Testo\Test]
    public function testNamingNone(ReaderInterface $reader): void
    {
        $r = new Registry($this->dbal);
        (new Entities(
            new TokenizerEntityLocator($this->locator, $reader),
            $reader,
            Entities::TABLE_NAMING_NONE,
        ))->run($r);
        (new MergeColumns($reader))->run($r);
        (new RenderTables())->run($r);

        \Testo\Assert::true($r->hasTable($r->getEntity('withTable')));

        $schema = $r->getTableSchema($r->getEntity('withTable'));

        \Testo\Assert::same($schema->getName(), 'with_table');
    }

    #[\Testo\Data\DataProvider('allReadersProvider')]
    #[\Testo\Test]
    public function testReadonlySchema(ReaderInterface $reader): void
    {
        $r = new Registry($this->dbal);
        (new Entities(new TokenizerEntityLocator($this->locator, $reader), $reader))->run($r);
        (new MergeColumns($reader))->run($r);
        (new RenderTables())->run($r);

        \Testo\Assert::true($r->hasTable($r->getEntity('simple')));

        $schema = $r->getTableSchema($r->getEntity('simple'));

        \Testo\Assert::true($schema->column('read_only_column')->isReadonlySchema());
    }

    #[\Testo\Data\DataProvider('foreignKeyDirectoriesDataProvider')]
    #[\Testo\Test]
    public function testForeignKeysAnnotationReader(
        ReaderInterface $reader,
        string $directory,
        string $outerKey = 'outer_key',
    ): void {
        $tokenizer = new Tokenizer(
            new TokenizerConfig([
                'directories' => [dirname(__DIR__, 3) . $directory],
                'exclude' => [],
            ]),
        );

        $locator = $tokenizer->classLocator();
        $registry = new Registry($this->dbal);

        (new Compiler())->compile($registry, [
            new Entities(new TokenizerEntityLocator($locator, $reader), $reader),
            new MergeColumns($reader),
            $t = new RenderTables(),
            new MergeIndexes($reader),
            new ForeignKeys(),
        ]);

        $t->getReflector()->run();

        $foreignKeys = $registry->getTableSchema($registry->getEntity('from'))->getForeignKeys();
        $expectedFk = array_shift($foreignKeys);

        \Testo\Assert::string($expectedFk->getTable())->contains('from');
        \Testo\Assert::string($expectedFk->getForeignTable())->contains('to');
        \Testo\Assert::same($expectedFk->getColumns(), ['inner_key']);
        \Testo\Assert::same($expectedFk->getForeignKeys(), [$outerKey]);
        \Testo\Assert::same($expectedFk->getDeleteRule(), 'CASCADE');
        \Testo\Assert::same($expectedFk->getUpdateRule(), 'CASCADE');
        \Testo\Assert::true($expectedFk->hasIndex());
    }
}
