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

abstract class RefersToTest extends BaseTest
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

        $this->assertArrayHasKey('parent', $schema['simple'][Schema::RELATIONS]);
        $this->assertSame(Relation::REFERS_TO, $schema['simple'][Schema::RELATIONS]['parent'][Relation::TYPE]);
        $this->assertSame("simple", $schema['simple'][Schema::RELATIONS]['parent'][Relation::TARGET]);

        $this->assertSame(
            'NO ACTION',
            $this->dbal->database('default')
                ->getDriver()
                ->getSchema('simples')
                ->foreignKey(['parent_id'])
                ->getDeleteRule()
        );
    }
}
