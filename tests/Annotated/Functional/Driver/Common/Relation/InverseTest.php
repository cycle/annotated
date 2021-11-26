<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Functional\Driver\Common\Relation;

use Cycle\Annotated\Entities;
use Cycle\Annotated\MergeColumns;
use Cycle\Annotated\MergeIndexes;
use Cycle\Annotated\Tests\Fixtures\Fixtures2\MarkedInterface;
use Cycle\Annotated\Tests\Functional\Driver\Common\BaseTest;
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

abstract class InverseTest extends BaseTest
{
    /**
     * @dataProvider allReadersProvider
     */
    public function testBelongsToOne(ReaderInterface $reader): void
    {
        $tokenizer = new Tokenizer(new TokenizerConfig([
            'directories' => [__DIR__ . '/../../../../Fixtures/Fixtures2'],
            'exclude' => [],
        ]));

        $locator = $tokenizer->classLocator();

        $r = new Registry($this->dbal);

        $schema = (new Compiler())->compile($r, [
            new Entities($locator, $reader),
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

        $this->assertArrayHasKey('parent', $schema['eComplete'][Schema::RELATIONS]);
        $this->assertSame(Relation::BELONGS_TO, $schema['eComplete'][Schema::RELATIONS]['parent'][Relation::TYPE]);
        $this->assertSame('simple', $schema['eComplete'][Schema::RELATIONS]['parent'][Relation::TARGET]);

        $this->assertArrayHasKey('child', $schema['simple'][Schema::RELATIONS]);
        $this->assertSame(Relation::HAS_ONE, $schema['simple'][Schema::RELATIONS]['child'][Relation::TYPE]);
        $this->assertSame('eComplete', $schema['simple'][Schema::RELATIONS]['child'][Relation::TARGET]);
    }

    /**
     * @dataProvider allReadersProvider
     */
    public function testBelongsToMany(ReaderInterface $reader): void
    {
        $tokenizer = new Tokenizer(new TokenizerConfig([
            'directories' => [__DIR__ . '/../../../../Fixtures/Fixtures2'],
            'exclude' => [],
        ]));

        $locator = $tokenizer->classLocator();

        $r = new Registry($this->dbal);

        $schema = (new Compiler())->compile($r, [
            new Entities($locator, $reader),
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

        $this->assertArrayHasKey('uncles', $schema['eComplete'][Schema::RELATIONS]);
        $this->assertSame(Relation::BELONGS_TO, $schema['eComplete'][Schema::RELATIONS]['uncles'][Relation::TYPE]);
        $this->assertSame('simple', $schema['eComplete'][Schema::RELATIONS]['uncles'][Relation::TARGET]);

        $this->assertArrayHasKey('stepKids', $schema['simple'][Schema::RELATIONS]);
        $this->assertSame(Relation::HAS_MANY, $schema['simple'][Schema::RELATIONS]['stepKids'][Relation::TYPE]);
        $this->assertSame('eComplete', $schema['simple'][Schema::RELATIONS]['stepKids'][Relation::TARGET]);
    }

    /**
     * @dataProvider allReadersProvider
     */
    public function testHasOne(ReaderInterface $reader): void
    {
        $tokenizer = new Tokenizer(new TokenizerConfig([
            'directories' => [__DIR__ . '/../../../../Fixtures/Fixtures2'],
            'exclude' => [],
        ]));

        $locator = $tokenizer->classLocator();

        $r = new Registry($this->dbal);

        $schema = (new Compiler())->compile($r, [
            new Entities($locator, $reader),
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

        $this->assertArrayHasKey('simple', $schema['user'][Schema::RELATIONS]);
        $this->assertSame(Relation::HAS_ONE, $schema['user'][Schema::RELATIONS]['simple'][Relation::TYPE]);
        $this->assertSame('simple', $schema['user'][Schema::RELATIONS]['simple'][Relation::TARGET]);

        $this->assertArrayHasKey('user', $schema['simple'][Schema::RELATIONS]);
        $this->assertSame(Relation::BELONGS_TO, $schema['simple'][Schema::RELATIONS]['user'][Relation::TYPE]);
        $this->assertSame('user', $schema['simple'][Schema::RELATIONS]['user'][Relation::TARGET]);
    }

    /**
     * @dataProvider allReadersProvider
     */
    public function testHasOneInverseLoad(ReaderInterface $reader): void
    {
        $tokenizer = new Tokenizer(new TokenizerConfig([
            'directories' => [__DIR__ . '/../../../../Fixtures/Fixtures5'],
            'exclude' => [],
        ]));

        $locator = $tokenizer->classLocator();

        $r = new Registry($this->dbal);

        $schema = (new Compiler())->compile($r, [
            new Entities($locator, $reader),
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

        $this->assertArrayHasKey('simple', $schema['user'][Schema::RELATIONS]);
        $this->assertSame(Relation::HAS_ONE, $schema['user'][Schema::RELATIONS]['simple'][Relation::TYPE]);
        $this->assertSame('simple', $schema['user'][Schema::RELATIONS]['simple'][Relation::TARGET]);

        $this->assertSame(Relation::LOAD_EAGER, $schema['user'][Schema::RELATIONS]['simple'][Relation::LOAD]);

        $this->assertArrayHasKey('user', $schema['simple'][Schema::RELATIONS]);
        $this->assertSame(Relation::BELONGS_TO, $schema['simple'][Schema::RELATIONS]['user'][Relation::TYPE]);
        $this->assertSame('user', $schema['simple'][Schema::RELATIONS]['user'][Relation::TARGET]);

        $this->assertSame(Relation::LOAD_PROMISE, $schema['simple'][Schema::RELATIONS]['user'][Relation::LOAD]);
    }

    /**
     * @dataProvider allReadersProvider
     */
    public function testBelongsTo(ReaderInterface $reader): void
    {
        $tokenizer = new Tokenizer(new TokenizerConfig([
            'directories' => [__DIR__ . '/../../../../Fixtures/Fixtures2'],
            'exclude' => [],
        ]));

        $locator = $tokenizer->classLocator();

        $r = new Registry($this->dbal);

        $schema = (new Compiler())->compile($r, [
            new Entities($locator, $reader),
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

        $this->assertArrayHasKey('owner', $schema['mark'][Schema::RELATIONS]);
        $this->assertSame(Relation::BELONGS_TO_MORPHED, $schema['mark'][Schema::RELATIONS]['owner'][Relation::TYPE]);
        $this->assertSame(MarkedInterface::class, $schema['mark'][Schema::RELATIONS]['owner'][Relation::TARGET]);

        $this->assertArrayHasKey('mark', $schema['user'][Schema::RELATIONS]);
        $this->assertSame(Relation::MORPHED_HAS_ONE, $schema['user'][Schema::RELATIONS]['mark'][Relation::TYPE]);
        $this->assertSame('mark', $schema['user'][Schema::RELATIONS]['mark'][Relation::TARGET]);

        $this->assertArrayHasKey('mark', $schema['simple'][Schema::RELATIONS]);
        $this->assertSame(Relation::MORPHED_HAS_ONE, $schema['simple'][Schema::RELATIONS]['mark'][Relation::TYPE]);
        $this->assertSame('mark', $schema['simple'][Schema::RELATIONS]['mark'][Relation::TARGET]);
    }
}
