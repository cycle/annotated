<?php

/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

declare(strict_types=1);

namespace Cycle\Annotated\Tests;

use Cycle\Annotated\Entities;
use Cycle\Annotated\MergeColumns;
use Cycle\Annotated\MergeIndexes;
use Cycle\Schema\Generator\RenderTables;
use Cycle\Schema\Generator\SyncTables;
use Cycle\Schema\Registry;
use Spiral\Tokenizer\Config\TokenizerConfig;
use Spiral\Tokenizer\Tokenizer;

abstract class TableTest extends BaseTest
{
    public function testColumnsRendered(): void
    {
        $r = new Registry($this->dbal);
        (new Entities($this->locator))->run($r);
        (new MergeColumns())->run($r);
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

    public function testIndexes(): void
    {
        $r = new Registry($this->dbal);

        (new Entities($this->locator))->run($r);
        (new MergeColumns())->run($r);
        (new RenderTables())->run($r);
        (new MergeIndexes())->run($r);
        (new SyncTables())->run($r);

        $this->assertTrue($r->hasTable($r->getEntity('withTable')));

        $schema = $r->getTableSchema($r->getEntity('withTable'));

        $this->assertTrue($schema->hasIndex(['name']));
        $this->assertTrue($schema->index(['name'])->isUnique());
        $this->assertSame('name_index', $schema->index(['name'])->getName());

        $this->assertTrue($schema->hasIndex(['status']));
    }

    public function testOrderedIndexes(): void
    {
        $tokenizer = new Tokenizer(new TokenizerConfig([
            'directories' => [__DIR__ . '/Fixtures9'],
            'exclude'     => [],
        ]));

        $locator = $tokenizer->classLocator();

        $r = new Registry($this->dbal);

        (new Entities($locator))->run($r);
        (new MergeColumns())->run($r);
        (new RenderTables())->run($r);
        (new MergeIndexes())->run($r);
        (new SyncTables())->run($r);

        $this->assertTrue($r->hasTable($r->getEntity('withTableOrderedIndex')));

        $schema = $r->getTableSchema($r->getEntity('withTableOrderedIndex'));

        $this->assertTrue($schema->hasIndex(['name', 'id DESC']));
    }

    public function testNamingDefault(): void
    {
        $r = new Registry($this->dbal);
        (new Entities($this->locator))->run($r);
        (new MergeColumns())->run($r);
        (new RenderTables())->run($r);

        $this->assertTrue($r->hasTable($r->getEntity('withTable')));

        $schema = $r->getTableSchema($r->getEntity('withTable'));

        $this->assertSame('with_tables', $schema->getName());
    }

    public function testNamingPluralize(): void
    {
        $r = new Registry($this->dbal);
        (new Entities($this->locator, null, Entities::TABLE_NAMING_PLURAL))->run($r);
        (new MergeColumns())->run($r);
        (new RenderTables())->run($r);

        $this->assertTrue($r->hasTable($r->getEntity('withTable')));

        $schema = $r->getTableSchema($r->getEntity('withTable'));

        $this->assertSame('with_tables', $schema->getName());
    }

    public function testNamingSingular(): void
    {
        $r = new Registry($this->dbal);
        (new Entities($this->locator, null, Entities::TABLE_NAMING_SINGULAR))->run($r);
        (new MergeColumns())->run($r);
        (new RenderTables())->run($r);

        $this->assertTrue($r->hasTable($r->getEntity('withTable')));

        $schema = $r->getTableSchema($r->getEntity('withTable'));

        $this->assertSame('with_table', $schema->getName());
    }

    public function testNamingNone(): void
    {
        $r = new Registry($this->dbal);
        (new Entities($this->locator, null, Entities::TABLE_NAMING_NONE))->run($r);
        (new MergeColumns())->run($r);
        (new RenderTables())->run($r);

        $this->assertTrue($r->hasTable($r->getEntity('withTable')));

        $schema = $r->getTableSchema($r->getEntity('withTable'));

        $this->assertSame('with_table', $schema->getName());
    }
}
