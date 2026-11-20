<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Functional\Driver\Common\Relation;

use Cycle\Annotated\Entities;
use Cycle\Annotated\Locator\TokenizerEntityLocator;
use Cycle\Annotated\MergeColumns;
use Cycle\Annotated\MergeIndexes;
use Cycle\Annotated\Tests\Fixtures\Fixtures2\MarkedInterface;
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
use Cycle\Schema\Generator\ValidateEntities;
use Cycle\Schema\Registry;
use PHPUnit\Framework\Attributes\DataProvider;
use Spiral\Attributes\ReaderInterface;
use Spiral\Tokenizer\Config\TokenizerConfig;
use Spiral\Tokenizer\Tokenizer;

abstract class InverseTestCase extends BaseTestCase
{
    #[\Testo\Data\DataProvider('allReadersProvider')]
    #[\Testo\Test]
    public function testBelongsToOne(ReaderInterface $reader): void
    {
        $tokenizer = new Tokenizer(new TokenizerConfig([
            'directories' => [__DIR__ . '/../../../../Fixtures/Fixtures2'],
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

        \Testo\Assert::array($schema['eComplete'][Schema::RELATIONS])->hasKeys('parent');
        \Testo\Assert::same($schema['eComplete'][Schema::RELATIONS]['parent'][Relation::TYPE], Relation::BELONGS_TO);
        \Testo\Assert::same($schema['eComplete'][Schema::RELATIONS]['parent'][Relation::TARGET], 'simple');

        \Testo\Assert::array($schema['simple'][Schema::RELATIONS])->hasKeys('child');
        \Testo\Assert::same($schema['simple'][Schema::RELATIONS]['child'][Relation::TYPE], Relation::HAS_ONE);
        \Testo\Assert::same($schema['simple'][Schema::RELATIONS]['child'][Relation::TARGET], 'eComplete');
    }

    #[\Testo\Data\DataProvider('allReadersProvider')]
    #[\Testo\Test]
    public function testBelongsToMany(ReaderInterface $reader): void
    {
        $tokenizer = new Tokenizer(new TokenizerConfig([
            'directories' => [__DIR__ . '/../../../../Fixtures/Fixtures2'],
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

        \Testo\Assert::array($schema['eComplete'][Schema::RELATIONS])->hasKeys('uncles');
        \Testo\Assert::same($schema['eComplete'][Schema::RELATIONS]['uncles'][Relation::TYPE], Relation::BELONGS_TO);
        \Testo\Assert::same($schema['eComplete'][Schema::RELATIONS]['uncles'][Relation::TARGET], 'simple');

        \Testo\Assert::array($schema['simple'][Schema::RELATIONS])->hasKeys('stepKids');
        \Testo\Assert::same($schema['simple'][Schema::RELATIONS]['stepKids'][Relation::TYPE], Relation::HAS_MANY);
        \Testo\Assert::same($schema['simple'][Schema::RELATIONS]['stepKids'][Relation::TARGET], 'eComplete');
    }

    #[\Testo\Data\DataProvider('allReadersProvider')]
    #[\Testo\Test]
    public function testHasOne(ReaderInterface $reader): void
    {
        $tokenizer = new Tokenizer(new TokenizerConfig([
            'directories' => [__DIR__ . '/../../../../Fixtures/Fixtures2'],
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

        \Testo\Assert::array($schema['user'][Schema::RELATIONS])->hasKeys('simple');
        \Testo\Assert::same($schema['user'][Schema::RELATIONS]['simple'][Relation::TYPE], Relation::HAS_ONE);
        \Testo\Assert::same($schema['user'][Schema::RELATIONS]['simple'][Relation::TARGET], 'simple');

        \Testo\Assert::array($schema['simple'][Schema::RELATIONS])->hasKeys('user');
        \Testo\Assert::same($schema['simple'][Schema::RELATIONS]['user'][Relation::TYPE], Relation::BELONGS_TO);
        \Testo\Assert::same($schema['simple'][Schema::RELATIONS]['user'][Relation::TARGET], 'user');
    }

    #[\Testo\Data\DataProvider('allReadersProvider')]
    #[\Testo\Test]
    public function testHasOneInverseLoad(ReaderInterface $reader): void
    {
        $tokenizer = new Tokenizer(new TokenizerConfig([
            'directories' => [__DIR__ . '/../../../../Fixtures/Fixtures5'],
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

        \Testo\Assert::array($schema['user'][Schema::RELATIONS])->hasKeys('simple');
        \Testo\Assert::same($schema['user'][Schema::RELATIONS]['simple'][Relation::TYPE], Relation::HAS_ONE);
        \Testo\Assert::same($schema['user'][Schema::RELATIONS]['simple'][Relation::TARGET], 'simple');

        \Testo\Assert::same($schema['user'][Schema::RELATIONS]['simple'][Relation::LOAD], Relation::LOAD_EAGER);

        \Testo\Assert::array($schema['simple'][Schema::RELATIONS])->hasKeys('user');
        \Testo\Assert::same($schema['simple'][Schema::RELATIONS]['user'][Relation::TYPE], Relation::BELONGS_TO);
        \Testo\Assert::same($schema['simple'][Schema::RELATIONS]['user'][Relation::TARGET], 'user');

        \Testo\Assert::same($schema['simple'][Schema::RELATIONS]['user'][Relation::LOAD], Relation::LOAD_PROMISE);
    }

    #[\Testo\Data\DataProvider('allReadersProvider')]
    #[\Testo\Test]
    public function testBelongsTo(ReaderInterface $reader): void
    {
        $tokenizer = new Tokenizer(new TokenizerConfig([
            'directories' => [__DIR__ . '/../../../../Fixtures/Fixtures2'],
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

        \Testo\Assert::array($schema['mark'][Schema::RELATIONS])->hasKeys('owner');
        \Testo\Assert::same($schema['mark'][Schema::RELATIONS]['owner'][Relation::TYPE], Relation::BELONGS_TO_MORPHED);
        \Testo\Assert::same($schema['mark'][Schema::RELATIONS]['owner'][Relation::TARGET], MarkedInterface::class);

        \Testo\Assert::array($schema['user'][Schema::RELATIONS])->hasKeys('mark');
        \Testo\Assert::same($schema['user'][Schema::RELATIONS]['mark'][Relation::TYPE], Relation::MORPHED_HAS_ONE);
        \Testo\Assert::same($schema['user'][Schema::RELATIONS]['mark'][Relation::TARGET], 'mark');

        \Testo\Assert::array($schema['simple'][Schema::RELATIONS])->hasKeys('mark');
        \Testo\Assert::same($schema['simple'][Schema::RELATIONS]['mark'][Relation::TYPE], Relation::MORPHED_HAS_ONE);
        \Testo\Assert::same($schema['simple'][Schema::RELATIONS]['mark'][Relation::TARGET], 'mark');
    }
}
