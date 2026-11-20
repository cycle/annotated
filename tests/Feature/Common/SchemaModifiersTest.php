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
use Cycle\Annotated\Tests\Fixtures\Fixtures1\Child;
use Cycle\Annotated\Tests\Fixtures\Fixtures17\MapperSegmentSchemaModifier;
use Cycle\Annotated\Tests\Fixtures\Fixtures17\ParentSegmentSchemaModifier;
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

final class SchemaModifiersTest extends BaseTestCase
{
    #[DataProvider('allReadersProvider')]
    #[Test]
    public function testModifierAnnotationShouldBeAddedToEntity(ReaderInterface $reader): void
    {
        $tokenizer = new Tokenizer(
            new TokenizerConfig([
                'directories' => [__DIR__ . '/../../Fixtures/Fixtures17'],
                'exclude' => [],
            ]),
        );

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

        $post = $r->getEntity('post');

        $modifiers = iterator_to_array($post->getSchemaModifiers());
        Assert::count($modifiers, 2);
        Assert::instanceOf($modifiers[0], ParentSegmentSchemaModifier::class);
        Assert::instanceOf($modifiers[1], MapperSegmentSchemaModifier::class);

        Assert::same($schema['post'][SchemaInterface::PARENT], \stdClass::class);
        Assert::same($schema['post'][SchemaInterface::MAPPER], Child::class);
    }
}
