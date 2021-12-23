<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Functional\Driver\Common\Relation;

use Cycle\Annotated\Entities;
use Cycle\Annotated\MergeColumns;
use Cycle\Annotated\MergeIndexes;
use Cycle\Annotated\Tests\Functional\Driver\Common\BaseTest;
use Cycle\ORM\Relation;
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
use Spiral\Attributes\AttributeReader;
use Spiral\Attributes\ReaderInterface;
use Spiral\Tokenizer\Config\TokenizerConfig;
use Spiral\Tokenizer\Tokenizer;

abstract class HasOneTest extends BaseTest
{
    /**
     * @dataProvider allReadersProvider
     */
    public function testRelation(ReaderInterface $reader): void
    {
        $r = new Registry($this->dbal);

        $schema = (new Compiler())->compile($r, [
            new Entities($this->locator, $reader),
            new ResetTables(),
            new MergeColumns($reader),
            new GenerateRelations(),
            new RenderTables(),
            new RenderRelations(),
            new MergeIndexes($reader),
            new SyncTables(),
            new GenerateTypecast(),
        ]);

        $this->assertArrayHasKey('one', $schema['simple'][Schema::RELATIONS]);
        $this->assertSame(Relation::HAS_ONE, $schema['simple'][Schema::RELATIONS]['one'][Relation::TYPE]);
        $this->assertSame('eComplete', $schema['simple'][Schema::RELATIONS]['one'][Relation::TARGET]);
        $this->assertSame(Relation::LOAD_PROMISE, $schema['simple'][Schema::RELATIONS]['one'][Relation::LOAD]);
    }

    public function testInnerOuterKeys(): void
    {
        $tokenizer = new Tokenizer(
            new TokenizerConfig([
                'directories' => [__DIR__ . '/../../../../Fixtures/Fixtures18'],
                'exclude' => [],
            ])
        );
        $reader = new AttributeReader();

        $locator = $tokenizer->classLocator();

        $r = new Registry($this->dbal);

        $schema = (new Compiler())->compile($r, [
            new Entities($locator, $reader),
            new MergeColumns($reader),
            new GenerateRelations(),
            $t = new RenderTables(),
            new RenderRelations(),
            new MergeIndexes($reader),
            new GenerateTypecast(),
        ]);

        // RENDER!
        $t->getReflector()->run();

        $this->assertSame(
            Relation::HAS_ONE,
            $schema['booking'][SchemaInterface::RELATIONS]['reservation'][Relation::TYPE]
        );
        $this->assertSame(
            'FlightRezervationId',
            $schema['booking'][Schema::RELATIONS]['reservation'][Relation::SCHEMA][Relation::INNER_KEY]
        );
    }
}
