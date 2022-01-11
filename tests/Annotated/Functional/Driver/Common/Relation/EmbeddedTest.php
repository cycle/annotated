<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Functional\Driver\Common\Relation;

use Cycle\Annotated\Embeddings;
use Cycle\Annotated\Entities;
use Cycle\Annotated\MergeColumns;
use Cycle\Annotated\MergeIndexes;
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
use Cycle\Schema\Registry;
use Spiral\Attributes\ReaderInterface;
use Spiral\Tokenizer\Config\TokenizerConfig;
use Spiral\Tokenizer\Tokenizer;

abstract class EmbeddedTest extends BaseTest
{
    /**
     * @dataProvider allReadersProvider
     */
    public function testRelation(ReaderInterface $reader): void
    {
        $tokenizer = new Tokenizer(new TokenizerConfig([
            'directories' => [__DIR__ . '/../../../../Fixtures/Fixtures6'],
            'exclude' => [],
        ]));

        $locator = $tokenizer->classLocator();

        $r = new Registry($this->dbal);

        $schema = (new Compiler())->compile($r, [
            new Embeddings($locator, $reader),
            new Entities($locator, $reader),
            new ResetTables(),
            new MergeColumns($reader),
            new GenerateRelations(),
            new RenderTables(),
            new RenderRelations(),
            new MergeIndexes($reader),
            new SyncTables(),
            new GenerateTypecast(),
        ]);

        $this->assertArrayHasKey('address', $schema['user'][Schema::RELATIONS]);
        $this->assertSame(Relation::EMBEDDED, $schema['user'][Schema::RELATIONS]['address'][Relation::TYPE]);

        $this->assertSame('user:address:address', $schema['user'][Schema::RELATIONS]['address'][Relation::TARGET]);
        $this->assertSame(Relation::LOAD_EAGER, $schema['user'][Schema::RELATIONS]['address'][Relation::LOAD]);
    }

    /**
     * @dataProvider allReadersProvider
     */
    public function testRelationLazyLoad(ReaderInterface $reader): void
    {
        $tokenizer = new Tokenizer(new TokenizerConfig([
            'directories' => [__DIR__ . '/../../../../Fixtures/Fixtures7'],
            'exclude' => [],
        ]));

        $locator = $tokenizer->classLocator();

        $r = new Registry($this->dbal);

        $schema = (new Compiler())->compile($r, [
            new Embeddings($locator, $reader),
            new Entities($locator, $reader),
            new ResetTables(),
            new MergeColumns($reader),
            new GenerateRelations(),
            new RenderTables(),
            new RenderRelations(),
            new MergeIndexes($reader),
            new SyncTables(),
            new GenerateTypecast(),
        ]);

        $this->assertArrayHasKey('address', $schema['user'][Schema::RELATIONS]);
        $this->assertSame(Relation::EMBEDDED, $schema['user'][Schema::RELATIONS]['address'][Relation::TYPE]);

        $this->assertSame('user:address:address', $schema['user'][Schema::RELATIONS]['address'][Relation::TARGET]);
        $this->assertSame(Relation::LOAD_PROMISE, $schema['user'][Schema::RELATIONS]['address'][Relation::LOAD]);
    }

    /**
     * @dataProvider allReadersProvider
     */
    public function testEmbeddedPrefix(ReaderInterface $reader): void
    {
        $tokenizer = new Tokenizer(new TokenizerConfig([
            'directories' => [__DIR__ . '/../../../../Fixtures/Fixtures6'],
            'exclude' => [],
        ]));

        $locator = $tokenizer->classLocator();

        $r = new Registry($this->dbal);

        $schema = (new Compiler())->compile($r, [
            new Embeddings($locator, $reader),
            new Entities($locator, $reader),
            new ResetTables(),
            new MergeColumns($reader),
            new GenerateRelations(),
            new RenderTables(),
            new RenderRelations(),
            new MergeIndexes($reader),
            new SyncTables(),
            new GenerateTypecast(),
        ]);

        $address = [
            'city' => 'address_city',
            'country' => 'address_country',
            'address' => 'address_address',
            'zipcode' => 'address_zipcode',
            'id' => 'id',
        ];
        $workAddress = [
            'city' => 'work_address_city',
            'country' => 'work_address_country',
            'address' => 'work_address_address',
            'zipcode' => 'work_address_zipcode',
            'id' => 'id',
        ];

        $this->assertSame($address, $schema['user:address:address'][Schema::COLUMNS]);
        $this->assertSame($workAddress, $schema['user:address:workAddress'][Schema::COLUMNS]);
    }
}
