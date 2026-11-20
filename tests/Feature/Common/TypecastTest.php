<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Feature\Common;

use Cycle\Annotated\Embeddings;
use Cycle\Annotated\Entities;
use Cycle\Annotated\Locator\TokenizerEmbeddingLocator;
use Cycle\Annotated\Locator\TokenizerEntityLocator;
use Cycle\Annotated\MergeColumns;
use Cycle\Annotated\MergeIndexes;
use Cycle\Annotated\Tests\Feature\BaseTestCase;
use Cycle\Annotated\Tests\Fixtures\Fixtures1\Typecast\Typecaster;
use Cycle\Annotated\Tests\Fixtures\Fixtures1\Typecast\UuidTypecaster;
use Cycle\Annotated\Tests\Fixtures\Fixtures19\BackedEnum;
use Cycle\Annotated\Tests\Fixtures\Fixtures19\BackedEnumWrapper;
use Cycle\ORM\Schema;
use Cycle\ORM\SchemaInterface;
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

final class TypecastTest extends BaseTestCase
{
    #[DataProvider('allReadersProvider')]
    #[Test]
    public function testEntityWithDefinedTypecastAsString(ReaderInterface $reader): void
    {
        $r = new Registry($this->dbal);

        $schema = (new Compiler())->compile($r, [
            new Entities(new TokenizerEntityLocator($this->locator, $reader), $reader),
            new MergeColumns($reader),
        ]);

        Assert::same($schema['simple'][Schema::TYPECAST_HANDLER], Typecaster::class);
    }

    #[DataProvider('allReadersProvider')]
    #[Test]
    public function testEntityWithDefinedTypecastAsArray(ReaderInterface $reader): void
    {
        $r = new Registry($this->dbal);

        $schema = (new Compiler())->compile($r, [
            new Entities(new TokenizerEntityLocator($this->locator, $reader), $reader),
            new MergeColumns($reader),
        ]);

        Assert::same($schema['tag'][Schema::TYPECAST_HANDLER], [
            Typecaster::class,
            UuidTypecaster::class,
            'foo',
        ]);
    }

    #[DataProvider('allReadersProvider')]
    #[Test]
    public function testBackedEnum(ReaderInterface $reader): void
    {
        $tokenizer = new Tokenizer(new TokenizerConfig([
            'directories' => [__DIR__ . '/../../Fixtures/Fixtures19'],
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

        Assert::same($schema['booking'][SchemaInterface::TYPECAST], [
            'bid' => 'int',
            'be' => BackedEnum::class,
            'bew' => [BackedEnumWrapper::class, 'typecast'],
        ]);
    }
}
