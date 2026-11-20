<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Feature\Common\Relation\Morphed;

use Cycle\Annotated\Entities;
use Cycle\Annotated\Locator\TokenizerEntityLocator;
use Cycle\Annotated\MergeColumns;
use Cycle\Annotated\MergeIndexes;
use Cycle\Annotated\Tests\Feature\BaseTestCase;
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
use Spiral\Attributes\ReaderInterface;
use Testo\Assert;
use Testo\Data\DataProvider;
use Testo\Test;

final class MorphedHasOneTest extends BaseTestCase
{
    #[DataProvider('allReadersProvider')]
    #[Test]
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

        Assert::array($schema['tag'][Schema::RELATIONS])->hasKeys('label');
        Assert::same($schema['tag'][Schema::RELATIONS]['label'][Relation::TYPE], Relation::MORPHED_HAS_ONE);
        Assert::same($schema['tag'][Schema::RELATIONS]['label'][Relation::TARGET], 'label');

        Assert::true($this->dbal->database('default')
            ->getDriver()
            ->getSchema('labels')
            ->hasColumn('owner_id'));

        Assert::true($this->dbal->database('default')
            ->getDriver()
            ->getSchema('labels')
            ->hasColumn('owner_role'));

        Assert::false($this->dbal->database('default')
            ->getDriver()
            ->getSchema('labels')
            ->hasIndex(['owner_id', 'owner_role']));
    }
}
