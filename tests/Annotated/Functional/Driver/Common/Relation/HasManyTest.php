<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Functional\Driver\Common\Relation;

use Cycle\Annotated\Entities;
use Cycle\Annotated\MergeColumns;
use Cycle\Annotated\MergeIndexes;
use Cycle\Annotated\Tests\Functional\Driver\Common\BaseTest;
use Cycle\ORM\Relation;
use Cycle\ORM\SchemaInterface;
use Cycle\ORM\SchemaInterface as Schema;
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

abstract class HasManyTest extends BaseTest
{
    /**
     * @dataProvider allReadersProvider
     */
    public function testRelation(ReaderInterface $reader): void
    {
        $r = new Registry($this->dbal);

        $schema = (new Compiler())->compile(
            $r,
            [
                new Entities($this->locator, $reader),
                new ResetTables(),
                new MergeColumns($reader),
                new GenerateRelations(),
                new RenderTables(),
                new RenderRelations(),
                new MergeIndexes($reader),
                new SyncTables(),
                new GenerateTypecast(),
            ]
        );

        $this->assertArrayHasKey('many', $schema['simple'][Schema::RELATIONS]);
        $this->assertSame(Relation::HAS_MANY, $schema['simple'][Schema::RELATIONS]['many'][Relation::TYPE]);
        $this->assertSame('withTable', $schema['simple'][Schema::RELATIONS]['many'][Relation::TARGET]);
        $this->assertSame(
            ['id' => ['>=' => 1]],
            $schema['simple'][Schema::RELATIONS]['many'][Relation::SCHEMA][Relation::WHERE]
        );
        $this->assertSame(
            ['id' => 'DESC'],
            $schema['simple'][Schema::RELATIONS]['many'][Relation::SCHEMA][Relation::ORDER_BY]
        );
        $this->assertSame(
            'bar',
            $schema['simple'][Schema::RELATIONS]['many'][Relation::SCHEMA][Relation::COLLECTION_TYPE]
        );
    }

    public function testInnerOuterKeys(): void
    {
        $tokenizer = new Tokenizer(new TokenizerConfig([
            'directories' => [__DIR__ . '/../../../../Fixtures/Fixtures18'],
            'exclude' => [],
        ]));
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

        $checks = [
            /** @see \Cycle\Annotated\Tests\Fixtures\Fixtures18\Reservation::$segments0 */
            ['segments0', 'rid', 'booking_reservation_rid'],
            /** @see \Cycle\Annotated\Tests\Fixtures\Fixtures18\Reservation::$segments1 */
            ['segments1', 'rid', 'parent_id'],
            /** @see \Cycle\Annotated\Tests\Fixtures\Fixtures18\Reservation::$segments2 */
            ['segments2', 'rid', 'parent_id'],
        ];
        foreach ($checks as [$name, $innerKey, $outerKey]) {
            $relation = $schema['booking_reservation'][SchemaInterface::RELATIONS][$name];

            $this->assertSame(Relation::HAS_MANY, $relation[Relation::TYPE], "$name: relation type");
            $this->assertSame(
                (array)$innerKey,
                (array)$relation[Relation::SCHEMA][Relation::INNER_KEY],
                "$name: Inner Key"
            );
            $this->assertSame(
                (array)$outerKey,
                (array)$relation[Relation::SCHEMA][Relation::OUTER_KEY],
                "$name: Outer Key"
            );
        }
    }
}
