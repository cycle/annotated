<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Functional\Driver\Common\Relation\Morphed;

use Cycle\Annotated\Entities;
use Cycle\Annotated\Locator\TokenizerEntityLocator;
use Cycle\Annotated\MergeColumns;
use Cycle\Annotated\MergeIndexes;
use Cycle\Annotated\Tests\Fixtures\Fixtures1\Collection\BaseCollection;
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

abstract class MorphedHasManyTestCase extends BaseTestCase
{
    #[\Testo\Data\DataProvider('allReadersProvider')]
    #[\Testo\Test]
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

        \Testo\Assert::array($schema['simple'][Schema::RELATIONS])->hasKeys('labels');
        \Testo\Assert::same($schema['simple'][Schema::RELATIONS]['labels'][Relation::TYPE], Relation::MORPHED_HAS_MANY);
        \Testo\Assert::same($schema['simple'][Schema::RELATIONS]['labels'][Relation::TARGET], 'label');

        \Testo\Assert::array($schema['withTable'][Schema::RELATIONS])->hasKeys('labels');
        \Testo\Assert::same($schema['withTable'][Schema::RELATIONS]['labels'][Relation::TYPE], Relation::MORPHED_HAS_MANY);

        \Testo\Assert::same($schema['withTable'][Schema::RELATIONS]['labels'][Relation::TARGET], 'label');

        \Testo\Assert::true($this->dbal->database('default')
            ->getDriver()
            ->getSchema('labels')
            ->hasColumn('owner_id'));

        \Testo\Assert::true($this->dbal->database('default')
            ->getDriver()
            ->getSchema('labels')
            ->hasColumn('owner_role'));

        \Testo\Assert::false($this->dbal->database('default')
            ->getDriver()
            ->getSchema('labels')
            ->hasIndex(['owner_id', 'owner_role']));

        \Testo\Assert::same($schema['simple'][\Cycle\ORM\SchemaInterface::RELATIONS]['labels'][Relation::SCHEMA][Relation::COLLECTION_TYPE], BaseCollection::class);
    }
}
