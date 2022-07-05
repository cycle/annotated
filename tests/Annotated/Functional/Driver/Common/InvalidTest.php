<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Functional\Driver\Common;

use Cycle\Annotated\Entities;
use Cycle\Annotated\Exception\AnnotationException;
use Cycle\Annotated\MergeColumns;
use Cycle\Annotated\MergeIndexes;
use Cycle\Schema\Compiler;
use Cycle\Schema\Exception\RelationException;
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

abstract class InvalidTest extends BaseTest
{
    /**
     * @dataProvider allReadersProvider
     */
    public function testInvalidRelation(ReaderInterface $reader): void
    {
        $tokenizer = new Tokenizer(new TokenizerConfig([
            'directories' => [__DIR__ . '/../../../Fixtures/Fixtures3'],
            'exclude' => [],
        ]));

        $locator = $tokenizer->classLocator();

        $r = new Registry($this->dbal);

        $this->expectException(RelationException::class);

        (new Compiler())->compile($r, [
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
    }

    /**
     * @dataProvider allReadersProvider
     */
    public function testNotDefinedColumnTypeShouldThrowAnException(ReaderInterface $reader): void
    {
        $this->expectException(AnnotationException::class);
        // $this->expectErrorMessage(
        //     'Some of required arguments [`type`] is missed on `Cycle\Annotated\Tests\Fixtures\Fixtures4\User.id.`'
        // );

        $tokenizer = new Tokenizer(new TokenizerConfig([
            'directories' => [__DIR__ . '/../../../Fixtures/Fixtures4'],
            'exclude' => [],
        ]));

        $locator = $tokenizer->classLocator();

        $r = new Registry($this->dbal);

        (new Compiler())->compile($r, [
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
    }
}
