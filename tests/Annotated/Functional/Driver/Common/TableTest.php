<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Functional\Driver\Common;

use Cycle\Annotated\Entities;
use Cycle\Annotated\Exception\AnnotationException;
use Cycle\Annotated\MergeColumns;
use Cycle\Annotated\MergeIndexes;
use Cycle\ORM\Schema;
use Cycle\Schema\Compiler;
use Cycle\Schema\Generator\RenderTables;
use Cycle\Schema\Generator\SyncTables;
use Cycle\Schema\Registry;
use Spiral\Attributes\AnnotationReader;
use Spiral\Attributes\ReaderInterface;
use Spiral\Tokenizer\Config\TokenizerConfig;
use Spiral\Tokenizer\Tokenizer;

abstract class TableTest extends BaseTest
{
    /**
     * @dataProvider allReadersProvider
     */
    public function testColumnsRendered(ReaderInterface $reader): void
    {
        $r = new Registry($this->dbal);
        (new Entities($this->locator, $reader))->run($r);
        (new MergeColumns($reader))->run($r);
        (new RenderTables())->run($r);

        $this->assertTrue($r->hasTable($r->getEntity('withTable')));

        $schema = $r->getTableSchema($r->getEntity('withTable'));

        $this->assertTrue($schema->hasColumn('name'));
        $this->assertSame('string', $schema->column('status')->getType());

        $this->assertTrue($schema->hasColumn('status'));

        $this->assertSame('enum', $schema->column('status')->getAbstractType());
        $this->assertSame('active', $schema->column('status')->getDefaultValue());
        $this->assertSame(['active', 'disabled'], $schema->column('status')->getEnumValues());
    }

    /**
     * @dataProvider allReadersProvider
     */
    public function testColumnWithDifferentColumnNameAndProperty(ReaderInterface $reader): void
    {
        $r = new Registry($this->dbal);
        $schema = (new Compiler())->compile(
            $r,
            [
                new Entities($this->locator, $reader),
                new MergeColumns($reader),
                new RenderTables(),
            ]
        );
        $this->assertArrayHasKey('withTable', $schema);
        $this->assertArrayHasKey('status_property', $schema['withTable'][Schema::COLUMNS]);
        $this->assertSame('status', $schema['withTable'][Schema::COLUMNS]['status_property']);
    }

    /**
     * @dataProvider allReadersProvider
     */
    public function testColumnWithoutColumnName(ReaderInterface $reader): void
    {
        $r = new Registry($this->dbal);
        $schema = (new Compiler())->compile(
            $r,
            [
                new Entities($this->locator, $reader),
                new MergeColumns($reader),
                new RenderTables(),
            ]
        );
        $this->assertArrayHasKey('withTable', $schema);
        $this->assertArrayHasKey('no_column_name', $schema['withTable'][Schema::COLUMNS]);
        $this->assertSame('no_column_name', $schema['withTable'][Schema::COLUMNS]['no_column_name']);
    }

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
            new Entities($locator, $reader),
            new MergeColumns($reader),
            new RenderTables(),
        ]);

        $this->assertArrayHasKey('orderedIdx', $schema);
        $this->assertArrayHasKey('other_name', $schema['orderedIdx'][Schema::COLUMNS]);
        $this->assertSame('other_name', $schema['orderedIdx'][Schema::COLUMNS]['other_name']);
    }

    public function testColumnWithPropertyInTableAnnotationByNamedKey(): void
    {
        $reader = new AnnotationReader();
        $tokenizer = new Tokenizer(new TokenizerConfig([
            'directories' => [__DIR__ . '/../../../Fixtures/Fixtures11'],
            'exclude' => [],
        ]));
        $locator = $tokenizer->classLocator();
        $r = new Registry($this->dbal);

        $this->expectException(AnnotationException::class);
        $this->expectExceptionMessage(
            'Can not use name "name1" for Column of the `badEntity` role, because the '
            . '"property" field of the metadata class has already been set to "name2".'
        );

        (new Compiler())->compile($r, [
            new Entities($locator, $reader),
            new MergeColumns($reader),
            new RenderTables(),
        ]);
    }

    /**
     * @dataProvider singularReadersProvider
     */
    public function testCompositePrimaryKey(ReaderInterface $reader): void
    {
        $r = new Registry($this->dbal);

        $tokenizer = new Tokenizer(new TokenizerConfig([
            'directories' => [__DIR__ . '/../../../Fixtures/Fixtures12'],
            'exclude' => [],
        ]));
        $locator = $tokenizer->classLocator();

        (new Entities($locator, $reader))->run($r);
        (new MergeColumns($reader))->run($r);
        (new RenderTables())->run($r);
        (new MergeIndexes($reader))->run($r);
        (new SyncTables())->run($r);

        $schema = $r->getTableSchema($r->getEntity('compositePost'));

        $this->assertEquals(['id', 'user_id'], $schema->getPrimaryKeys());
    }

    /**
     * @dataProvider singularReadersProvider
     */
    public function testIndexWithEmptyColumnsShouldThrowAnException(ReaderInterface $reader): void
    {
        $this->expectException(\Cycle\Annotated\Exception\AnnotationException::class);
        $this->expectErrorMessage('Invalid index definition for `compositePost`. Column list can\'t be empty.');

        $r = new Registry($this->dbal);

        $tokenizer = new Tokenizer(new TokenizerConfig([
            'directories' => [__DIR__ . '/../../../Fixtures/Fixtures13'],
            'exclude' => [],
        ]));
        $locator = $tokenizer->classLocator();

        (new Entities($locator, $reader))->run($r);
        (new MergeColumns($reader))->run($r);
        (new RenderTables())->run($r);
        (new MergeIndexes($reader))->run($r);
    }

    /**
     * @dataProvider allReadersProvider
     */
    public function testIndexes(ReaderInterface $reader): void
    {
        $r = new Registry($this->dbal);

        (new Entities($this->locator, $reader))->run($r);
        (new MergeColumns($reader))->run($r);
        (new RenderTables())->run($r);
        (new MergeIndexes($reader))->run($r);
        (new SyncTables())->run($r);

        $this->assertTrue($r->hasTable($r->getEntity('withTable')));

        $schema = $r->getTableSchema($r->getEntity('withTable'));

        $this->assertTrue($schema->hasIndex(['name']));
        $this->assertTrue($schema->index(['name'])->isUnique());
        $this->assertSame('name_index', $schema->index(['name'])->getName());

        $this->assertTrue($schema->hasIndex(['status']));
    }

    /**
     * @dataProvider singularReadersProvider
     */
    public function testOrderedIndexes(ReaderInterface $reader): void
    {
        $tokenizer = new Tokenizer(new TokenizerConfig([
            'directories' => [__DIR__ . '/../../../Fixtures/Fixtures9'],
            'exclude' => [],
        ]));

        $locator = $tokenizer->classLocator();

        $r = new Registry($this->dbal);

        (new Entities($locator, $reader))->run($r);
        (new MergeColumns($reader))->run($r);
        (new RenderTables())->run($r);
        (new MergeIndexes($reader))->run($r);
        (new SyncTables())->run($r);

        $this->assertTrue($r->hasTable($r->getEntity('orderedIdx')));

        $schema = $r->getTableSchema($r->getEntity('orderedIdx'));

        $this->assertTrue($schema->hasColumn('name'));
        $this->assertTrue($schema->hasIndex(['name', 'id DESC']));
    }

    /**
     * @dataProvider allReadersProvider
     */
    public function testNamingDefault(ReaderInterface $reader): void
    {
        $r = new Registry($this->dbal);
        (new Entities($this->locator, $reader))->run($r);
        (new MergeColumns($reader))->run($r);
        (new RenderTables())->run($r);

        $this->assertTrue($r->hasTable($r->getEntity('withTable')));

        $schema = $r->getTableSchema($r->getEntity('withTable'));

        $this->assertSame('with_tables', $schema->getName());
    }

    /**
     * @dataProvider allReadersProvider
     */
    public function testNamingPluralize(ReaderInterface $reader): void
    {
        $r = new Registry($this->dbal);
        (new Entities($this->locator, $reader, Entities::TABLE_NAMING_PLURAL))->run($r);
        (new MergeColumns($reader))->run($r);
        (new RenderTables())->run($r);

        $this->assertTrue($r->hasTable($r->getEntity('withTable')));

        $schema = $r->getTableSchema($r->getEntity('withTable'));

        $this->assertSame('with_tables', $schema->getName());
    }

    /**
     * @dataProvider allReadersProvider
     */
    public function testNamingSingular(ReaderInterface $reader): void
    {
        $r = new Registry($this->dbal);
        (new Entities($this->locator, $reader, Entities::TABLE_NAMING_SINGULAR))->run($r);
        (new MergeColumns($reader))->run($r);
        (new RenderTables())->run($r);

        $this->assertTrue($r->hasTable($r->getEntity('withTable')));

        $schema = $r->getTableSchema($r->getEntity('withTable'));

        $this->assertSame('with_table', $schema->getName());
    }

    /**
     * @dataProvider allReadersProvider
     */
    public function testNamingNone(ReaderInterface $reader): void
    {
        $r = new Registry($this->dbal);
        (new Entities($this->locator, $reader, Entities::TABLE_NAMING_NONE))->run($r);
        (new MergeColumns($reader))->run($r);
        (new RenderTables())->run($r);

        $this->assertTrue($r->hasTable($r->getEntity('withTable')));

        $schema = $r->getTableSchema($r->getEntity('withTable'));

        $this->assertSame('with_table', $schema->getName());
    }
}
