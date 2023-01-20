<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Functional\Driver\Common;

use Cycle\Annotated\Embeddings;
use Cycle\Annotated\Entities;
use Cycle\Annotated\MergeColumns;
use Cycle\Annotated\MergeIndexes;
use Cycle\Annotated\TableInheritance;
use Cycle\Annotated\Tests\Traits\TableTrait;
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

abstract class BigIntegerTest extends BaseTest
{
    use TableTrait;

    private Registry $registry;

    public function setUp(): void
    {
        parent::setUp();

        $this->makeTable(
            table: 'big_integer',
            columns: [
                'id' => 'primary',
            ]
        );
    }

    /**
     * @dataProvider allReadersProvider
     */
    public function testBigIntegerPk(ReaderInterface $reader): void
    {
        $this->compile($reader, 'Fixtures22/Single');

        $this->assertSame(
            'bigInteger',
            $this->registry->getEntity('person')->getFields()->get('id')->getType()
        );
        $this->assertTrue($this->registry->getEntity('person')->getFields()->get('id')->isPrimary());
    }

    /**
     * @dataProvider allReadersProvider
     */
    public function testBigIntegerPkWithJti(ReaderInterface $reader): void
    {
        $this->compile($reader, 'Fixtures22/Jti');

        $this->assertSame(
            'bigInteger',
            $this->registry->getEntity('person')->getFields()->get('id')->getType()
        );
        $this->assertTrue($this->registry->getEntity('person')->getFields()->get('id')->isPrimary());
        $this->assertTrue($this->registry->hasEntity('buyer'));
    }

    private function compile(ReaderInterface $reader, string $fixtures): array
    {
        $tokenizer = new Tokenizer(
            new TokenizerConfig([
                'directories' => [sprintf(__DIR__ . '/../../../Fixtures/%s', $fixtures)],
                'exclude' => [],
            ])
        );

        $this->registry = new Registry($this->dbal);
        $locator = $tokenizer->classLocator();

        return (new Compiler())->compile($this->registry, [
            new ResetTables(),
            new Embeddings($locator, $reader),
            new Entities($locator, $reader),
            new TableInheritance($reader),
            new MergeColumns($reader),
            new GenerateRelations(),
            new RenderTables(),
            new RenderRelations(),
            new MergeIndexes($reader),
            new SyncTables(),
            new GenerateTypecast(),
        ]);
    }
}
