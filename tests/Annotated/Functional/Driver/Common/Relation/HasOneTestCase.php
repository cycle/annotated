<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Functional\Driver\Common\Relation;

use Cycle\Annotated\Entities;
use Cycle\Annotated\Locator\TokenizerEntityLocator;
use Cycle\Annotated\MergeColumns;
use Cycle\Annotated\MergeIndexes;
use Cycle\Annotated\Tests\Functional\Driver\Common\BaseTestCase;
use Cycle\ORM\Relation;
use Cycle\ORM\SchemaInterface;
use Cycle\Schema\Compiler;
use Cycle\Schema\Generator\GenerateRelations;
use Cycle\Schema\Generator\GenerateTypecast;
use Cycle\Schema\Generator\RenderRelations;
use Cycle\Schema\Generator\RenderTables;
use Cycle\Schema\Generator\ResetTables;
use Cycle\Schema\Generator\SyncTables;
use Cycle\Schema\Registry;
use PHPUnit\Framework\Attributes\DataProvider;
use Spiral\Attributes\AttributeReader;
use Spiral\Attributes\ReaderInterface;
use Spiral\Tokenizer\Config\TokenizerConfig;
use Spiral\Tokenizer\Tokenizer;

abstract class HasOneTestCase extends BaseTestCase
{
    #[\Testo\Data\DataProvider('allReadersProvider')]
    #[\Testo\Test]
    public function testRelation(ReaderInterface $reader): void
    {
        $r = new Registry($this->dbal);

        $schema = (new Compiler())->compile($r, [
            new Entities(new TokenizerEntityLocator($this->locator, $reader), $reader),
            new ResetTables(),
            new MergeColumns($reader),
            new GenerateRelations(),
            new RenderTables(),
            new RenderRelations(),
            new MergeIndexes($reader),
            new SyncTables(),
            new GenerateTypecast(),
        ]);

        \Testo\Assert::array($schema['simple'][SchemaInterface::RELATIONS])->hasKeys('one');
        \Testo\Assert::same($schema['simple'][SchemaInterface::RELATIONS]['one'][Relation::TYPE], Relation::HAS_ONE);
        \Testo\Assert::same($schema['simple'][SchemaInterface::RELATIONS]['one'][Relation::TARGET], 'eComplete');
        \Testo\Assert::same($schema['simple'][SchemaInterface::RELATIONS]['one'][Relation::LOAD], Relation::LOAD_PROMISE);
    }

    #[\Testo\Test]
    public function testInnerOuterKeys(): void
    {
        $tokenizer = new Tokenizer(
            new TokenizerConfig([
                'directories' => [__DIR__ . '/../../../../Fixtures/Fixtures18'],
                'exclude' => [],
            ]),
        );
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
            /** @see \Cycle\Annotated\Tests\Fixtures\Fixtures18\Booking::$reservation0 */
            ['reservation0', 'bid', 'booking_bid'],
            /** @see \Cycle\Annotated\Tests\Fixtures\Fixtures18\Booking::$reservation1 */
            ['reservation1', 'reserv_id', 'rid'],
            /** @see \Cycle\Annotated\Tests\Fixtures\Fixtures18\Booking::$reservation2 */
            ['reservation2', 'reserv_id', 'rid'],
        ];

        foreach ($checks as [$name, $innerKey, $outerKey]) {
            $relation = $schema['booking'][SchemaInterface::RELATIONS][$name];

            \Testo\Assert::same($relation[Relation::TYPE], Relation::HAS_ONE, "$name: relation type");
            \Testo\Assert::same((array) $relation[Relation::SCHEMA][Relation::INNER_KEY], (array) $innerKey, "$name: Inner Key");
            \Testo\Assert::same((array) $relation[Relation::SCHEMA][Relation::OUTER_KEY], (array) $outerKey, "$name: Outer Key");
        }
        \Testo\Assert::array($schema['booking'][SchemaInterface::COLUMNS])->doesNotHaveKeys('id_reservation');
    }
}
