<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Feature\Common\Relation\Morphed;

use Cycle\Annotated\Entities;
use Cycle\Annotated\Locator\TokenizerEntityLocator;
use Cycle\Annotated\MergeColumns;
use Cycle\Annotated\MergeIndexes;
use Cycle\Annotated\Tests\Feature\BaseTestCase;
use Cycle\Annotated\Tests\Fixtures\Fixtures1\Constrain\SomeConstrain;
use Cycle\Annotated\Tests\Fixtures\Fixtures1\LabelledInterface;
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
use Testo\Assert;
use Testo\Data\DataProvider;
use Testo\Test;

final class BelongsToMorphedTest extends BaseTestCase
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

        Assert::array($schema['label'][Schema::RELATIONS])->hasKeys('owner');
        Assert::same($schema['label'][Schema::RELATIONS]['owner'][Relation::TYPE], Relation::BELONGS_TO_MORPHED);

        Assert::same($schema['label'][Schema::RELATIONS]['owner'][Relation::TARGET], LabelledInterface::class);

        Assert::same($schema['label'][Schema::SCOPE], SomeConstrain::class);
    }
}
