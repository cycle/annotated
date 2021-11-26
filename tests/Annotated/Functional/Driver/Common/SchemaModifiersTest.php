<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Functional\Driver\Common;

use Cycle\Annotated\Embeddings;
use Cycle\Annotated\Entities;
use Cycle\Annotated\MergeColumns;
use Cycle\Annotated\MergeIndexes;
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

abstract class SchemaModifiersTest extends BaseTest
{
    /**
     * @dataProvider allReadersProvider
     */
    public function testModifierAnnotationShouldBeAddedToEntity(ReaderInterface $reader): void
    {
        $tokenizer = new Tokenizer(
            new TokenizerConfig([
                'directories' => [__DIR__ . '/../../../Fixtures/Fixtures17'],
                'exclude' => [],
            ])
        );

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

        $post = $r->getEntity('post');

        $modifiers = iterator_to_array($post->getSchemaModifiers());
        $this->assertCount(2, $modifiers);
        $this->assertInstanceOf(ParentSegmentSchemaModifier::class, $modifiers[0]);
        $this->assertInstanceOf(MapperSegmentSchemaModifier::class, $modifiers[1]);

        $this->assertSame(\stdClass::class, $schema['post'][SchemaInterface::PARENT]);
        $this->assertSame(Child::class, $schema['post'][SchemaInterface::MAPPER]);
    }
}
