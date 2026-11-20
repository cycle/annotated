<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Feature\Common\Relation\Morphed;

use Cycle\Annotated\Entities;
use Cycle\Annotated\Locator\TokenizerEntityLocator;
use Cycle\Annotated\MergeColumns;
use Cycle\Annotated\MergeIndexes;
use Cycle\Annotated\Tests\Feature\BaseTestCase;
use Cycle\Annotated\Tests\Fixtures\Fixtures1\Collection\BaseCollection;
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

final class MorphedHasManyTest extends BaseTestCase
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

        Assert::array($schema['simple'][Schema::RELATIONS])->hasKeys('labels');
        Assert::same($schema['simple'][Schema::RELATIONS]['labels'][Relation::TYPE], Relation::MORPHED_HAS_MANY);
        Assert::same($schema['simple'][Schema::RELATIONS]['labels'][Relation::TARGET], 'label');

        Assert::array($schema['withTable'][Schema::RELATIONS])->hasKeys('labels');
        Assert::same($schema['withTable'][Schema::RELATIONS]['labels'][Relation::TYPE], Relation::MORPHED_HAS_MANY);

        Assert::same($schema['withTable'][Schema::RELATIONS]['labels'][Relation::TARGET], 'label');

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

        Assert::same($schema['simple'][\Cycle\ORM\SchemaInterface::RELATIONS]['labels'][Relation::SCHEMA][Relation::COLLECTION_TYPE], BaseCollection::class);
    }
}
