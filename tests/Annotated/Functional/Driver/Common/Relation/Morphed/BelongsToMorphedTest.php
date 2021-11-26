<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Functional\Driver\Common\Relation\Morphed;

use Cycle\Annotated\Entities;
use Cycle\Annotated\MergeColumns;
use Cycle\Annotated\MergeIndexes;
use Cycle\Annotated\Tests\Fixtures\Fixtures1\Constrain\SomeConstrain;
use Cycle\Annotated\Tests\Fixtures\Fixtures1\LabelledInterface;
use Cycle\Annotated\Tests\Functional\Driver\Common\BaseTest;
use Cycle\ORM\Relation;
use Cycle\ORM\SchemaInterface as Schema;
use Cycle\Schema\Compiler;
use Cycle\Schema\Generator\GenerateRelations;
use Cycle\Schema\Generator\GenerateTypecast;
use Cycle\Schema\Generator\RenderRelations;
use Cycle\Schema\Generator\RenderTables;
use Cycle\Schema\Generator\ResetTables;
use Cycle\Schema\Generator\SyncTables;
use Cycle\Schema\Registry;
use Spiral\Attributes\ReaderInterface;

abstract class BelongsToMorphedTest extends BaseTest
{
    /**
     * @dataProvider allReadersProvider
     */
    public function testRelation(ReaderInterface $reader): void
    {
        $r = new Registry($this->dbal);

        $schema = (new Compiler())->compile($r, [
            new Entities($this->locator, $reader),
            new ResetTables(),
            new MergeColumns($reader),
            new GenerateRelations(),
            new RenderTables(),
            new RenderRelations(),
            new MergeIndexes($reader),
            new SyncTables(),
            new GenerateTypecast(),
        ]);

        $this->assertArrayHasKey('owner', $schema['label'][Schema::RELATIONS]);
        $this->assertSame(
            Relation::BELONGS_TO_MORPHED,
            $schema['label'][Schema::RELATIONS]['owner'][Relation::TYPE]
        );

        $this->assertSame(
            LabelledInterface::class,
            $schema['label'][Schema::RELATIONS]['owner'][Relation::TARGET]
        );

        $this->assertSame(SomeConstrain::class, $schema['label'][Schema::SCOPE]);
    }
}
