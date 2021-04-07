<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests;

use Cycle\Annotated\Entities;
use Cycle\Annotated\MergeColumns;
use Cycle\Annotated\MergeIndexes;
use Cycle\Schema\Generator\RenderTables;
use Cycle\Schema\Generator\SyncTables;
use Cycle\Schema\Registry;
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
            'directories' => [__DIR__ . '/Fixtures9'],
            'exclude'     => [],
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
