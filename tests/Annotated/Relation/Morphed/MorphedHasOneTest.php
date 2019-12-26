<?php

/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

declare(strict_types=1);

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

abstract class MorphedHasOneTest extends BaseTest
{
    public function testRelation(): void
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

        $this->assertArrayHasKey('label', $schema['tag'][Schema::RELATIONS]);
        $this->assertSame(Relation::MORPHED_HAS_ONE, $schema['tag'][Schema::RELATIONS]['label'][Relation::TYPE]);
        $this->assertSame('label', $schema['tag'][Schema::RELATIONS]['label'][Relation::TARGET]);

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
