<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Functional\Driver\Common\Relation\Morphed;

use Cycle\Annotated\Entities;
use Cycle\Annotated\Locator\TokenizerEntityLocator;
use Cycle\Annotated\MergeColumns;
use Cycle\Annotated\MergeIndexes;
use Cycle\Annotated\Tests\Functional\Driver\Common\BaseTestCase;
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
use PHPUnit\Framework\Attributes\DataProvider;
use Spiral\Attributes\ReaderInterface;

abstract class MorphedHasOneTestCase extends BaseTestCase
{
    #[DataProvider('allReadersProvider')]
    public function testRelation(ReaderInterface $reader): void
    {
        $r = new Registry($this->dbal);

        $schema = (new Compiler())->compile($r, [
            new Entities(new TokenizerEntityLocator($this->locator, $reader), $reader),
            new ResetTables(),
            new MergeColumns($reader),
            new GenerateRelations(),
            new RenderTables(),
            new RenderRelations(),
            new MergeIndexes($reader),
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
                ->hasColumn('owner_id'),
        );

        $this->assertTrue(
            $this->dbal->database('default')
                ->getDriver()
                ->getSchema('labels')
                ->hasColumn('owner_role'),
        );

        $this->assertFalse(
            $this->dbal->database('default')
                ->getDriver()
                ->getSchema('labels')
                ->hasIndex(['owner_id', 'owner_role']),
        );
    }
}
