<?php declare(strict_types=1);
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Cycle\Annotated\Tests\Relation\Morphed;

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

abstract class MorphedHasManyTest extends BaseTest
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

        $this->assertArrayHasKey('labels', $schema['simple'][Schema::RELATIONS]);
        $this->assertSame(Relation::MORPHED_HAS_MANY, $schema['simple'][Schema::RELATIONS]['labels'][Relation::TYPE]);
        $this->assertSame("label", $schema['simple'][Schema::RELATIONS]['labels'][Relation::TARGET]);

        $this->assertArrayHasKey('labels', $schema['withTable'][Schema::RELATIONS]);
        $this->assertSame(
            Relation::MORPHED_HAS_MANY,
            $schema['withTable'][Schema::RELATIONS]['labels'][Relation::TYPE]
        );

        $this->assertSame("label", $schema['withTable'][Schema::RELATIONS]['labels'][Relation::TARGET]);

        $this->assertTrue(
            $this->dbal->database('default')
                ->getDriver()
                ->getSchema('labels')
                ->hasColumn('owner_id')
        );

        $this->assertTrue(
            $this->dbal->database('default')
                ->getDriver()
                ->getSchema('labels')
                ->hasColumn('owner_role')
        );

        $this->assertFalse(
            $this->dbal->database('default')
                ->getDriver()
                ->getSchema('labels')
                ->hasIndex(['owner_id', 'owner_role'])
        );
    }
}