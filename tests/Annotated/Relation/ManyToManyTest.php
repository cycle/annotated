<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Relation;

use Cycle\Annotated\Entities;
use Cycle\Annotated\MergeColumns;
use Cycle\Annotated\MergeIndexes;
use Cycle\Annotated\Tests\BaseTest;
use Cycle\Annotated\Tests\Fixtures\Collection\BaseCollection;
use Cycle\Database\Schema\AbstractTable;
use Cycle\ORM\Relation;
use Cycle\ORM\SchemaInterface as Schema;
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

use function PHPUnit\Framework\assertCount;

abstract class ManyToManyTest extends BaseTest
{
    /**
     * @dataProvider allReadersProvider
     */
    public function testPivotPrimaryKeys(ReaderInterface $reader): void
    {
        $tokenizer = new Tokenizer(new TokenizerConfig([
            'directories' => [dirname(__DIR__) . '/Fixtures15'],
            'exclude' => [],
        ]));

        $locator = $tokenizer->classLocator();

        // $this->logger->display();
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

        $table = current(array_filter(
            $t->getReflector()->getTables(),
            static fn (AbstractTable $t): bool => str_contains($t->getName(), 'context')
        ));
        assert($table instanceof AbstractTable);

        // Check MTM relation in the Schema
        $this->assertSame(Relation::MANY_TO_MANY, $schema['post'][Schema::RELATIONS]['tags'][Relation::TYPE]);
        $this->assertSame('tag', $schema['post'][Schema::RELATIONS]['tags'][Relation::TARGET]);
        $this->assertSame(
            'context',
            $schema['post'][Schema::RELATIONS]['tags'][Relation::SCHEMA][Relation::THROUGH_ENTITY]
        );

        // Check table
        assertCount(3, $table->getIndexes());
    }

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

        $this->assertArrayHasKey('tags', $schema['withTable'][Schema::RELATIONS]);

        $this->assertSame(
            Relation::MANY_TO_MANY,
            $schema['withTable'][Schema::RELATIONS]['tags'][Relation::TYPE]
        );

        $this->assertSame(
            'tag',
            $schema['withTable'][Schema::RELATIONS]['tags'][Relation::TARGET]
        );

        $this->assertSame(
            'tagContext',
            $schema['withTable'][Schema::RELATIONS]['tags'][Relation::SCHEMA][Relation::THROUGH_ENTITY]
        );

        $this->assertSame(
            ['withTable_id'],
            $schema['withTable'][Schema::RELATIONS]['tags'][Relation::SCHEMA][Relation::THROUGH_INNER_KEY]
        );

        $this->assertSame(
            ['tag_id'],
            $schema['withTable'][Schema::RELATIONS]['tags'][Relation::SCHEMA][Relation::THROUGH_OUTER_KEY]
        );

        $this->assertSame(
            ['id' => ['>=' => '1']],
            $schema['withTable'][Schema::RELATIONS]['tags'][Relation::SCHEMA][Relation::WHERE]
        );
        $this->assertSame(
            ['id' => 'DESC'],
            $schema['withTable'][Schema::RELATIONS]['tags'][Relation::SCHEMA][Relation::ORDER_BY]
        );
        $this->assertSame(
            BaseCollection::class,
            $schema['withTable'][Schema::RELATIONS]['tags'][Relation::SCHEMA][Relation::COLLECTION_TYPE]
        );
    }

    /**
     * @dataProvider allReadersProvider
     */
    public function testThoughRelation(ReaderInterface $reader): void
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

        $this->assertArrayHasKey('tags', $schema['withTable2'][Schema::RELATIONS]);

        $this->assertSame(
            Relation::MANY_TO_MANY,
            $schema['withTable2'][Schema::RELATIONS]['tags'][Relation::TYPE]
        );

        $this->assertSame(
            'tag',
            $schema['withTable2'][Schema::RELATIONS]['tags'][Relation::TARGET]
        );

        $this->assertSame(
            'tagContext',
            $schema['withTable2'][Schema::RELATIONS]['tags'][Relation::SCHEMA][Relation::THROUGH_ENTITY]
        );

        $this->assertSame(
            'withTable2_id',
            $schema['withTable2'][Schema::RELATIONS]['tags'][Relation::SCHEMA][Relation::THROUGH_INNER_KEY]
        );

        $this->assertSame(
            'tag_id',
            $schema['withTable2'][Schema::RELATIONS]['tags'][Relation::SCHEMA][Relation::THROUGH_OUTER_KEY]
        );

        $this->assertSame(
            ['id' => ['>=' => '1']],
            $schema['withTable2'][Schema::RELATIONS]['tags'][Relation::SCHEMA][Relation::WHERE]
        );
        $this->assertSame(
            ['id' => 'DESC'],
            $schema['withTable2'][Schema::RELATIONS]['tags'][Relation::SCHEMA][Relation::ORDER_BY]
        );

        $this->assertSame(
            'label',
            $schema['throughThough'][Schema::RELATIONS]['tags'][Relation::SCHEMA][Relation::THROUGH_ENTITY]
        );
    }
}
