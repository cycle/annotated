<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Feature\Common\Relation;

use Cycle\Annotated\Entities;
use Cycle\Annotated\Locator\TokenizerEntityLocator;
use Cycle\Annotated\MergeColumns;
use Cycle\Annotated\MergeIndexes;
use Cycle\Annotated\Tests\Feature\BaseTestCase;
use Cycle\Annotated\Tests\Fixtures\Fixtures2\MarkedInterface;
use Cycle\ORM\Relation;
use Cycle\ORM\Schema;
use Cycle\Schema\Compiler;
use Cycle\Schema\Generator\GenerateRelations;
use Cycle\Schema\Generator\GenerateTypecast;
use Cycle\Schema\Generator\RenderRelations;
use Cycle\Schema\Generator\RenderTables;
use Cycle\Schema\Generator\ResetTables;
use Cycle\Schema\Generator\SyncTables;
use Cycle\Schema\Generator\ValidateEntities;
use Cycle\Schema\Registry;
use Spiral\Attributes\ReaderInterface;
use Spiral\Tokenizer\Config\TokenizerConfig;
use Spiral\Tokenizer\Tokenizer;
use Testo\Assert;
use Testo\Data\DataProvider;
use Testo\Test;

final class InverseTest extends BaseTestCase
{
    #[DataProvider('allReadersProvider')]
    #[Test]
    public function testBelongsToOne(ReaderInterface $reader): void
    {
        $tokenizer = new Tokenizer(new TokenizerConfig([
            'directories' => [__DIR__ . '/../../../Fixtures/Fixtures2'],
            'exclude' => [],
        ]));

        $locator = $tokenizer->classLocator();

        $r = new Registry($this->dbal);

        $schema = (new Compiler())->compile($r, [
            new Entities(new TokenizerEntityLocator($locator, $reader), $reader),
            new ResetTables(),
            new MergeColumns($reader),
            new GenerateRelations(),
            new ValidateEntities(),
            new RenderTables(),
            new RenderRelations(),
            new MergeIndexes($reader),
            new SyncTables(),
            new GenerateTypecast(),
        ]);

        Assert::array($schema['eComplete'][Schema::RELATIONS])->hasKeys('parent');
        Assert::same($schema['eComplete'][Schema::RELATIONS]['parent'][Relation::TYPE], Relation::BELONGS_TO);
        Assert::same($schema['eComplete'][Schema::RELATIONS]['parent'][Relation::TARGET], 'simple');

        Assert::array($schema['simple'][Schema::RELATIONS])->hasKeys('child');
        Assert::same($schema['simple'][Schema::RELATIONS]['child'][Relation::TYPE], Relation::HAS_ONE);
        Assert::same($schema['simple'][Schema::RELATIONS]['child'][Relation::TARGET], 'eComplete');
    }

    #[DataProvider('allReadersProvider')]
    #[Test]
    public function testBelongsToMany(ReaderInterface $reader): void
    {
        $tokenizer = new Tokenizer(new TokenizerConfig([
            'directories' => [__DIR__ . '/../../../Fixtures/Fixtures2'],
            'exclude' => [],
        ]));

        $locator = $tokenizer->classLocator();

        $r = new Registry($this->dbal);

        $schema = (new Compiler())->compile($r, [
            new Entities(new TokenizerEntityLocator($locator, $reader), $reader),
            new ResetTables(),
            new MergeColumns($reader),
            new GenerateRelations(),
            new ValidateEntities(),
            new RenderTables(),
            new RenderRelations(),
            new MergeIndexes($reader),
            new SyncTables(),
            new GenerateTypecast(),
        ]);

        Assert::array($schema['eComplete'][Schema::RELATIONS])->hasKeys('uncles');
        Assert::same($schema['eComplete'][Schema::RELATIONS]['uncles'][Relation::TYPE], Relation::BELONGS_TO);
        Assert::same($schema['eComplete'][Schema::RELATIONS]['uncles'][Relation::TARGET], 'simple');

        Assert::array($schema['simple'][Schema::RELATIONS])->hasKeys('stepKids');
        Assert::same($schema['simple'][Schema::RELATIONS]['stepKids'][Relation::TYPE], Relation::HAS_MANY);
        Assert::same($schema['simple'][Schema::RELATIONS]['stepKids'][Relation::TARGET], 'eComplete');
    }

    #[DataProvider('allReadersProvider')]
    #[Test]
    public function testHasOne(ReaderInterface $reader): void
    {
        $tokenizer = new Tokenizer(new TokenizerConfig([
            'directories' => [__DIR__ . '/../../../Fixtures/Fixtures2'],
            'exclude' => [],
        ]));

        $locator = $tokenizer->classLocator();

        $r = new Registry($this->dbal);

        $schema = (new Compiler())->compile($r, [
            new Entities(new TokenizerEntityLocator($locator, $reader), $reader),
            new ResetTables(),
            new MergeColumns($reader),
            new GenerateRelations(),
            new ValidateEntities(),
            new RenderTables(),
            new RenderRelations(),
            new MergeIndexes($reader),
            new SyncTables(),
            new GenerateTypecast(),
        ]);

        Assert::array($schema['user'][Schema::RELATIONS])->hasKeys('simple');
        Assert::same($schema['user'][Schema::RELATIONS]['simple'][Relation::TYPE], Relation::HAS_ONE);
        Assert::same($schema['user'][Schema::RELATIONS]['simple'][Relation::TARGET], 'simple');

        Assert::array($schema['simple'][Schema::RELATIONS])->hasKeys('user');
        Assert::same($schema['simple'][Schema::RELATIONS]['user'][Relation::TYPE], Relation::BELONGS_TO);
        Assert::same($schema['simple'][Schema::RELATIONS]['user'][Relation::TARGET], 'user');
    }

    #[DataProvider('allReadersProvider')]
    #[Test]
    public function testHasOneInverseLoad(ReaderInterface $reader): void
    {
        $tokenizer = new Tokenizer(new TokenizerConfig([
            'directories' => [__DIR__ . '/../../../Fixtures/Fixtures5'],
            'exclude' => [],
        ]));

        $locator = $tokenizer->classLocator();

        $r = new Registry($this->dbal);

        $schema = (new Compiler())->compile($r, [
            new Entities(new TokenizerEntityLocator($locator, $reader), $reader),
            new ResetTables(),
            new MergeColumns($reader),
            new GenerateRelations(),
            new ValidateEntities(),
            new RenderTables(),
            new RenderRelations(),
            new MergeIndexes($reader),
            new SyncTables(),
            new GenerateTypecast(),
        ]);

        Assert::array($schema['user'][Schema::RELATIONS])->hasKeys('simple');
        Assert::same($schema['user'][Schema::RELATIONS]['simple'][Relation::TYPE], Relation::HAS_ONE);
        Assert::same($schema['user'][Schema::RELATIONS]['simple'][Relation::TARGET], 'simple');

        Assert::same($schema['user'][Schema::RELATIONS]['simple'][Relation::LOAD], Relation::LOAD_EAGER);

        Assert::array($schema['simple'][Schema::RELATIONS])->hasKeys('user');
        Assert::same($schema['simple'][Schema::RELATIONS]['user'][Relation::TYPE], Relation::BELONGS_TO);
        Assert::same($schema['simple'][Schema::RELATIONS]['user'][Relation::TARGET], 'user');

        Assert::same($schema['simple'][Schema::RELATIONS]['user'][Relation::LOAD], Relation::LOAD_PROMISE);
    }

    #[DataProvider('allReadersProvider')]
    #[Test]
    public function testBelongsTo(ReaderInterface $reader): void
    {
        $tokenizer = new Tokenizer(new TokenizerConfig([
            'directories' => [__DIR__ . '/../../../Fixtures/Fixtures2'],
            'exclude' => [],
        ]));

        $locator = $tokenizer->classLocator();

        $r = new Registry($this->dbal);

        $schema = (new Compiler())->compile($r, [
            new Entities(new TokenizerEntityLocator($locator, $reader), $reader),
            new ResetTables(),
            new MergeColumns($reader),
            new GenerateRelations(),
            new ValidateEntities(),
            new RenderTables(),
            new RenderRelations(),
            new MergeIndexes($reader),
            new SyncTables(),
            new GenerateTypecast(),
        ]);

        Assert::array($schema['mark'][Schema::RELATIONS])->hasKeys('owner');
        Assert::same($schema['mark'][Schema::RELATIONS]['owner'][Relation::TYPE], Relation::BELONGS_TO_MORPHED);
        Assert::same($schema['mark'][Schema::RELATIONS]['owner'][Relation::TARGET], MarkedInterface::class);

        Assert::array($schema['user'][Schema::RELATIONS])->hasKeys('mark');
        Assert::same($schema['user'][Schema::RELATIONS]['mark'][Relation::TYPE], Relation::MORPHED_HAS_ONE);
        Assert::same($schema['user'][Schema::RELATIONS]['mark'][Relation::TARGET], 'mark');

        Assert::array($schema['simple'][Schema::RELATIONS])->hasKeys('mark');
        Assert::same($schema['simple'][Schema::RELATIONS]['mark'][Relation::TYPE], Relation::MORPHED_HAS_ONE);
        Assert::same($schema['simple'][Schema::RELATIONS]['mark'][Relation::TARGET], 'mark');
    }
}
