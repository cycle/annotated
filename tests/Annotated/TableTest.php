<?php declare(strict_types=1);
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Cycle\Annotated\Tests;

use Cycle\Annotated\Entities;
use Cycle\Annotated\MergeColumns;
use Cycle\Annotated\MergeIndexes;
use Cycle\Schema\Generator\RenderTables;
use Cycle\Schema\Generator\SyncTables;
use Cycle\Schema\Registry;

abstract class TableTest extends BaseTest
{
    public function testColumnsRendered()
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

    public function testIndexes()
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
}