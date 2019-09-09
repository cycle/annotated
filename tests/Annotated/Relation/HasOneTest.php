<?php declare(strict_types=1);
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Cycle\Annotated\Tests\Relation;

use Cycle\Annotated\Entities;
use Cycle\Annotated\MergeColumns;
use Cycle\Annotated\MergeIndexes;
use Cycle\Annotated\Tests\BaseTest;
use Cycle\ORM\Relation;
use Cycle\ORM\Schema;
use Cycle\Schema\Compiler;
use Cycle\Schema\Generator\GenerateRelations;
use Cycle\Schema\Generator\GenerateTypecast;
use Cycle\Schema\Generator\RenderRelations;
use Cycle\Schema\Generator\RenderTables;
use Cycle\Schema\Generator\ResetTables;
use Cycle\Schema\Generator\SyncTables;
use Cycle\Schema\Registry;
use Cycle\Schema\Relation\HasOne as HasOneRelation;

abstract class HasOneTest extends BaseTest
{
    public function testRelation()
    {
        $r = new Registry($this->dbal);

        $schema = (new Compiler())->compile($r, [
            new Entities($this->locator),
            new ResetTables(),
            new MergeColumns(),
            new GenerateRelations(),
            new RenderTables(),
            new RenderRelations(),
            new MergeIndexes(),
            new SyncTables(),
            new GenerateTypecast(),
        ]);

        $this->assertArrayHasKey('one', $schema['simple'][Schema::RELATIONS]);
        $this->assertSame(Relation::HAS_ONE, $schema['simple'][Schema::RELATIONS]['one'][Relation::TYPE]);
        $this->assertSame("eComplete", $schema['simple'][Schema::RELATIONS]['one'][Relation::TARGET]);
        $this->assertSame(Relation::LOAD_PROMISE, $schema['simple'][Schema::RELATIONS]['one'][Relation::LOAD]);
    }
}
