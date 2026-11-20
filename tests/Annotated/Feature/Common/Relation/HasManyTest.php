<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Feature\Common\Relation;

use Cycle\Annotated\Entities;
use Cycle\Annotated\Locator\TokenizerEntityLocator;
use Cycle\Annotated\MergeColumns;
use Cycle\Annotated\MergeIndexes;
use Cycle\Annotated\Tests\Feature\BaseTestCase;
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
use Testo\Assert;
use Testo\Data\DataProvider;
use Testo\Test;

final class HasManyTest extends BaseTestCase
{
    #[DataProvider('allReadersProvider')]
    #[Test]
    public function testRelation(ReaderInterface $reader): void
    {
        $r = new Registry($this->dbal);

        $schema = (new Compiler())->compile(
            $r,
            [
                new Entities(new TokenizerEntityLocator($this->locator, $reader), $reader),
                new ResetTables(),
                new MergeColumns($reader),
                new GenerateRelations(),
                new RenderTables(),
                new RenderRelations(),
                new MergeIndexes($reader),
                new SyncTables(),
                new GenerateTypecast(),
            ],
        );

        Assert::array($schema['simple'][Schema::RELATIONS])->hasKeys('many');
        Assert::same($schema['simple'][Schema::RELATIONS]['many'][Relation::TYPE], Relation::HAS_MANY);
        Assert::same($schema['simple'][Schema::RELATIONS]['many'][Relation::TARGET], 'withTable');
        Assert::same($schema['simple'][Schema::RELATIONS]['many'][Relation::SCHEMA][Relation::WHERE], ['id' => ['>=' => 1]]);
        Assert::same($schema['simple'][Schema::RELATIONS]['many'][Relation::SCHEMA][Relation::ORDER_BY], ['id' => 'DESC']);
        Assert::same($schema['simple'][Schema::RELATIONS]['many'][Relation::SCHEMA][Relation::COLLECTION_TYPE], 'bar');
    }

    #[Test]
    public function testInnerOuterKeys(): void
    {
        $tokenizer = new Tokenizer(new TokenizerConfig([
            'directories' => [__DIR__ . '/../../../Fixtures/Fixtures18'],
            'exclude' => [],
        ]));
        $reader = new AttributeReader();

        $locator = $tokenizer->classLocator();

        $r = new Registry($this->dbal);

        $schema = (new Compiler())->compile($r, [
            new Entities(new TokenizerEntityLocator($locator, $reader), $reader),
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

            Assert::same($relation[Relation::TYPE], Relation::HAS_MANY, "$name: relation type");
            Assert::same((array) $relation[Relation::SCHEMA][Relation::INNER_KEY], (array) $innerKey, "$name: Inner Key");
            Assert::same((array) $relation[Relation::SCHEMA][Relation::OUTER_KEY], (array) $outerKey, "$name: Outer Key");
        }
    }
}
