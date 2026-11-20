<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Feature\Common\Relation;

use Cycle\Annotated\Embeddings;
use Cycle\Annotated\Entities;
use Cycle\Annotated\Locator\TokenizerEmbeddingLocator;
use Cycle\Annotated\Locator\TokenizerEntityLocator;
use Cycle\Annotated\MergeColumns;
use Cycle\Annotated\MergeIndexes;
use Cycle\Annotated\Tests\Feature\BaseTestCase;
use Cycle\Annotated\Tests\Fixtures\Fixtures26\CityTypecast;
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
use Testo\Assert;
use Testo\Data\DataProvider;
use Testo\Test;

final class EmbeddedTest extends BaseTestCase
{
    #[DataProvider('allReadersProvider')]
    #[Test]
    public function testRelation(ReaderInterface $reader): void
    {
        $tokenizer = new Tokenizer(new TokenizerConfig([
            'directories' => [__DIR__ . '/../../../Fixtures/Fixtures6'],
            'exclude' => [],
        ]));

        $locator = $tokenizer->classLocator();

        $r = new Registry($this->dbal);

        $schema = (new Compiler())->compile($r, [
            new Embeddings(new TokenizerEmbeddingLocator($locator, $reader), $reader),
            new Entities(new TokenizerEntityLocator($locator, $reader), $reader),
            new ResetTables(),
            new MergeColumns($reader),
            new GenerateRelations(),
            new RenderTables(),
            new RenderRelations(),
            new MergeIndexes($reader),
            new SyncTables(),
            new GenerateTypecast(),
        ]);

        Assert::array($schema['user'][Schema::RELATIONS])->hasKeys('address');
        Assert::same($schema['user'][Schema::RELATIONS]['address'][Relation::TYPE], Relation::EMBEDDED);

        Assert::same($schema['user'][Schema::RELATIONS]['address'][Relation::TARGET], 'user:address:address');
        Assert::same($schema['user'][Schema::RELATIONS]['address'][Relation::LOAD], Relation::LOAD_EAGER);
        Assert::true($r->getTableSchema($r->getEntity('user'))->hasIndex(['address_zipcode']));
    }

    #[DataProvider('allReadersProvider')]
    #[Test]
    public function testRelationLazyLoad(ReaderInterface $reader): void
    {
        $tokenizer = new Tokenizer(new TokenizerConfig([
            'directories' => [__DIR__ . '/../../../Fixtures/Fixtures7'],
            'exclude' => [],
        ]));

        $locator = $tokenizer->classLocator();

        $r = new Registry($this->dbal);

        $schema = (new Compiler())->compile($r, [
            new Embeddings(new TokenizerEmbeddingLocator($locator, $reader), $reader),
            new Entities(new TokenizerEntityLocator($locator, $reader), $reader),
            new ResetTables(),
            new MergeColumns($reader),
            new GenerateRelations(),
            new RenderTables(),
            new RenderRelations(),
            new MergeIndexes($reader),
            new SyncTables(),
            new GenerateTypecast(),
        ]);

        Assert::array($schema['user'][Schema::RELATIONS])->hasKeys('address');
        Assert::same($schema['user'][Schema::RELATIONS]['address'][Relation::TYPE], Relation::EMBEDDED);

        Assert::same($schema['user'][Schema::RELATIONS]['address'][Relation::TARGET], 'user:address:address');
        Assert::same($schema['user'][Schema::RELATIONS]['address'][Relation::LOAD], Relation::LOAD_PROMISE);
        Assert::true($r->getTableSchema($r->getEntity('user:address:address'))->hasIndex(['address_zipcode']));
    }

    #[DataProvider('allReadersProvider')]
    #[Test]
    public function testEmbeddedPrefix(ReaderInterface $reader): void
    {
        $tokenizer = new Tokenizer(new TokenizerConfig([
            'directories' => [__DIR__ . '/../../../Fixtures/Fixtures6'],
            'exclude' => [],
        ]));

        $locator = $tokenizer->classLocator();

        $r = new Registry($this->dbal);

        $schema = (new Compiler())->compile($r, [
            new Embeddings(new TokenizerEmbeddingLocator($locator, $reader), $reader),
            new Entities(new TokenizerEntityLocator($locator, $reader), $reader),
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

        Assert::same($schema['user:address:address'][Schema::COLUMNS], $address);
        Assert::same($schema['user:address:workAddress'][Schema::COLUMNS], $workAddress);
    }

    #[DataProvider('allReadersProvider')]
    #[Test]
    public function testEmbeddedTypecast(ReaderInterface $reader): void
    {
        $tokenizer = new Tokenizer(new TokenizerConfig([
            'directories' => [__DIR__ . '/../../../Fixtures/Fixtures26'],
            'exclude' => [],
        ]));

        $locator = $tokenizer->classLocator();

        $r = new Registry($this->dbal);

        $schema = (new Compiler())->compile($r, [
            new Embeddings(new TokenizerEmbeddingLocator($locator, $reader), $reader),
            new Entities(new TokenizerEntityLocator($locator, $reader), $reader),
            new ResetTables(),
            new MergeColumns($reader),
            new GenerateRelations(),
            new RenderTables(),
            new RenderRelations(),
            new MergeIndexes($reader),
            new SyncTables(),
            new GenerateTypecast(),
        ]);

        Assert::same($schema['user:address:address'][Schema::TYPECAST_HANDLER][0], CityTypecast::class);
    }
}
