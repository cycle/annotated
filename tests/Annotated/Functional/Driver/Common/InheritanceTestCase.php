<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Functional\Driver\Common;

use Cycle\Annotated\Embeddings;
use Cycle\Annotated\Entities;
use Cycle\Annotated\Locator\TokenizerEmbeddingLocator;
use Cycle\Annotated\Locator\TokenizerEntityLocator;
use Cycle\Annotated\MergeColumns;
use Cycle\Annotated\MergeIndexes;
use Cycle\Annotated\TableInheritance;
use Cycle\Annotated\Tests\Fixtures\Fixtures16\Ceo;
use Cycle\Annotated\Tests\Fixtures\Fixtures16\Customer;
use Cycle\Annotated\Tests\Fixtures\Fixtures16\Employee;
use Cycle\Annotated\Tests\Fixtures\Fixtures16\Executive;
use Cycle\Annotated\Tests\Fixtures\Fixtures16\Person;
use Cycle\Annotated\Tests\Fixtures\Fixtures16\Tool;
use Cycle\Annotated\Tests\Fixtures\Fixtures27\BtJoined;
use Cycle\Annotated\Tests\Fixtures\Fixtures27\BtParent;
use Cycle\Annotated\Tests\Fixtures\Fixtures27\BtTarget;
use Cycle\Annotated\Tests\Fixtures\Fixtures27\JgLeaf;
use Cycle\Annotated\Tests\Fixtures\Fixtures27\JgMid;
use Cycle\Annotated\Tests\Fixtures\Fixtures27\JgParent;
use Cycle\Annotated\Tests\Fixtures\Fixtures27\JgTarget;
use Cycle\Annotated\Tests\Fixtures\Fixtures27\SoParent;
use Cycle\Annotated\Tests\Fixtures\Fixtures27\SoSeparate;
use Cycle\Annotated\Tests\Fixtures\Fixtures27\SoSti;
use Cycle\Annotated\Tests\Fixtures\Fixtures27\SoTarget;
use Cycle\Annotated\Tests\Fixtures\Fixtures27\StChild;
use Cycle\Annotated\Tests\Fixtures\Fixtures27\StParent;
use Cycle\Annotated\Tests\Fixtures\Fixtures27\StTarget;
use Cycle\Annotated\Tests\Fixtures\Fixtures27\TrJti;
use Cycle\Annotated\Tests\Fixtures\Fixtures27\TrParent;
use Cycle\Annotated\Tests\Fixtures\Fixtures27\TrTarget;
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
use Spiral\Tokenizer\ClassesInterface;
use Spiral\Tokenizer\Config\TokenizerConfig;
use Spiral\Tokenizer\Tokenizer;

abstract class InheritanceTestCase extends BaseTestCase
{
    #[\Testo\Data\DataProvider('allReadersProvider')]
    #[\Testo\Test]
    public function testTableInheritance(ReaderInterface $reader): void
    {
        $tokenizer = new Tokenizer(
            new TokenizerConfig([
                'directories' => [__DIR__ . '/../../../Fixtures/Fixtures16'],
                'exclude' => [__DIR__ . '/Fixtures16/CatWithoutParent.php'],
            ]),
        );

        $locator = $tokenizer->classLocator();

        $r = new Registry($this->dbal);

        $schema = (new Compiler())->compile($r, [
            new Embeddings(new TokenizerEmbeddingLocator($locator, $reader), $reader),
            new Entities(new TokenizerEntityLocator($locator, $reader), $reader),
            new TableInheritance($reader),
            new ResetTables(),
            new MergeColumns($reader),
            new GenerateRelations(),
            new RenderTables(),
            new RenderRelations(),
            new MergeIndexes($reader),
            new SyncTables(),
            new GenerateTypecast(),
        ]);

        // Person  - {discriminator: type, children: employee, customer}
        // Employee - Single table inheritance {value: employee}
        // Customer - Single table inheritance {value: foo_customer}
        // Executive - Joined table inheritance {outerKey: foo_id}
        // Ceo - Single table inheritance {value: ceo}
        // Beaver - Separate table

        // Tool
        \Testo\Assert::array($schema)->hasKeys('tool');

        // Person
        \Testo\Assert::count($schema['person'][SchemaInterface::CHILDREN], 3);
        \Testo\Assert::equals($schema['person'][SchemaInterface::CHILDREN], [
            'employee' => Employee::class,
            'foo_customer' => Customer::class,
            'ceo' => Ceo::class,
        ]);
        \Testo\Assert::same($schema['person'][SchemaInterface::DISCRIMINATOR], 'type');
        \Testo\Assert::equals($schema['person'][SchemaInterface::COLUMNS], [
            'foo_id' => 'id',
            'name' => 'name',
            'type' => 'type',
            'salary' => 'salary',
            'bar' => 'bar',
            // 'bonus' => 'bonus', // JTI
            'preferences' => 'preferences',
            'stocks' => 'stocks',
            'tool_id' => 'tool_id',
            // 'teethAmount' => 'teeth_amount', // Not child
        ]);
        $this->assertEmpty($schema['person'][SchemaInterface::PARENT] ?? null);
        $this->assertEmpty($schema['person'][SchemaInterface::PARENT_KEY] ?? null);
        \Testo\Assert::same($schema['person'][SchemaInterface::TABLE], 'people');
        \Testo\Assert::count($schema['person'][SchemaInterface::RELATIONS], 1);

        // Employee
        \Testo\Assert::array($schema)->hasKeys('employee');
        \Testo\Assert::count($schema['employee'], 1);
        \Testo\Assert::same($schema['employee'][SchemaInterface::ENTITY], Employee::class);
        \Testo\Assert::null($schema['employee'][SchemaInterface::TABLE] ?? null);
        \Testo\Assert::count($schema['employee'][SchemaInterface::RELATIONS] ?? [], 0);

        // Customer
        \Testo\Assert::array($schema)->hasKeys('customer');
        \Testo\Assert::count($schema['customer'], 1);
        \Testo\Assert::same($schema['customer'][SchemaInterface::ENTITY], Customer::class);
        \Testo\Assert::null($schema['customer'][SchemaInterface::TABLE] ?? null);
        \Testo\Assert::count($schema['customer'][SchemaInterface::RELATIONS] ?? [], 0);

        // Executive
        \Testo\Assert::same($schema['executive'][SchemaInterface::PARENT], 'employee');
        \Testo\Assert::same($schema['executive'][SchemaInterface::PARENT_KEY], 'foo_id');
        \Testo\Assert::same($schema['executive'][SchemaInterface::TABLE], 'executives');
        \Testo\Assert::equals($schema['executive'][SchemaInterface::COLUMNS], [
            'bonus' => 'bonus',
            'proxyFieldWithAnnotation' => 'proxy',
            'foo_id' => 'id',
            'hidden' => 'hidden',
            'added_tool_id' => 'added_tool_id',
        ]);
        \Testo\Assert::count($schema['executive'][SchemaInterface::RELATIONS], 1);

        // Executive2
        \Testo\Assert::same($schema['executive2'][SchemaInterface::PARENT], 'executive');
        \Testo\Assert::same($schema['executive2'][SchemaInterface::PARENT_KEY], 'foo_id');
        \Testo\Assert::equals($schema['executive2'][SchemaInterface::COLUMNS], ['foo_id' => 'id']);
        \Testo\Assert::count($schema['executive2'][SchemaInterface::RELATIONS], 0);

        // Ceo
        \Testo\Assert::array($schema)->hasKeys('ceo');
        \Testo\Assert::count($schema['ceo'], 1);
        \Testo\Assert::same($schema['ceo'][SchemaInterface::ENTITY], Ceo::class);
        \Testo\Assert::null($schema['ceo'][SchemaInterface::TABLE] ?? null);
        \Testo\Assert::count($schema['ceo'][SchemaInterface::RELATIONS] ?? [], 0);

        // Beaver
        $this->assertEmpty($schema['beaver'][SchemaInterface::DISCRIMINATOR] ?? null);
        $this->assertEmpty($schema['beaver'][SchemaInterface::PARENT] ?? null);
        $this->assertEmpty($schema['beaver'][SchemaInterface::PARENT_KEY] ?? null);
        $this->assertEmpty($schema['beaver'][SchemaInterface::CHILDREN] ?? null);
        \Testo\Assert::same($schema['beaver'][SchemaInterface::TABLE], 'beavers');
        \Testo\Assert::equals($schema['beaver'][SchemaInterface::COLUMNS], [
            'teethAmount' => 'teeth_amount',
            'foo_id' => 'id',
            'name' => 'name',
            'type' => 'type',
            'hidden' => 'hidden',
            'tool_id' => 'tool_id',
        ]);
        \Testo\Assert::count($schema['beaver'][SchemaInterface::RELATIONS] ?? [], 1);
    }

    #[\Testo\Test]
    public function testTableInheritanceWithIncorrectClassesOrder(): void
    {
        $r = new Registry($this->dbal);
        $reader = new AttributeReader();
        $locator = $this->createMock(ClassesInterface::class);
        $locator
            ->method('getClasses')
            ->willReturn([
                new \ReflectionClass(Employee::class),
                new \ReflectionClass(Executive::class),
                new \ReflectionClass(Person::class),
                new \ReflectionClass(Tool::class),
            ]);

        $schema = (new Compiler())->compile($r, [
            new Embeddings(new TokenizerEmbeddingLocator($locator, $reader), $reader),
            new Entities(new TokenizerEntityLocator($locator, $reader), $reader),
            new TableInheritance($reader),
            new ResetTables(),
            new MergeColumns($reader),
            new GenerateRelations(),
            new RenderTables(),
            new RenderRelations(),
            new MergeIndexes($reader),
            new SyncTables(),
            new GenerateTypecast(),
        ]);

        \Testo\Assert::same($schema['executive'][SchemaInterface::TABLE], 'executives');
        \Testo\Assert::null($schema['employee'][SchemaInterface::TABLE] ?? null);
        \Testo\Assert::same($schema['person'][SchemaInterface::TABLE], 'people');
    }

    #[\Testo\Test]
    public function testJtiChildDoesNotInheritBelongsToColumn(): void
    {
        $schema = $this->compileWithClasses([BtTarget::class, BtParent::class, BtJoined::class]);

        // BtParent owns the BelongsTo + its FK column
        \Testo\Assert::array($schema['btParent'][SchemaInterface::COLUMNS])->hasKeys('target_id');
        \Testo\Assert::count($schema['btParent'][SchemaInterface::RELATIONS], 1);

        // JTI child must not duplicate the BelongsTo FK column nor inherit the relation
        \Testo\Assert::array($schema['btJoined'][SchemaInterface::COLUMNS])->doesNotHaveKeys('target_id');
        \Testo\Assert::count($schema['btJoined'][SchemaInterface::RELATIONS] ?? [], 0);
    }

    #[\Testo\Test]
    public function testStiChildOwnRelationMergesIntoParent(): void
    {
        $schema = $this->compileWithClasses([StTarget::class, StParent::class, StChild::class]);

        // Relation declared on the STI child must end up on the parent after merge
        \Testo\Assert::array($schema['stParent'][SchemaInterface::COLUMNS])->hasKeys('target_id');
        \Testo\Assert::count($schema['stParent'][SchemaInterface::RELATIONS], 1);
        \Testo\Assert::array($schema['stParent'][SchemaInterface::RELATIONS])->hasKeys('target');
    }

    #[\Testo\Test]
    public function testSeparateEntityExtendingStiChildKeepsInheritedRelation(): void
    {
        $schema = $this->compileWithClasses([
            SoTarget::class,
            SoParent::class,
            SoSti::class,
            SoSeparate::class,
        ]);

        // Separate entity (no Inheritance attr) extending an STI child must keep ancestor relations
        $this->assertEmpty($schema['soSeparate'][SchemaInterface::PARENT] ?? null);
        \Testo\Assert::notNull($schema['soSeparate'][SchemaInterface::TABLE] ?? null);
        \Testo\Assert::array($schema['soSeparate'][SchemaInterface::COLUMNS])->hasKeys('target_id');
        \Testo\Assert::array($schema['soSeparate'][SchemaInterface::COLUMNS])->hasKeys('extra');
        \Testo\Assert::count($schema['soSeparate'][SchemaInterface::RELATIONS] ?? [], 1);
        \Testo\Assert::array($schema['soSeparate'][SchemaInterface::RELATIONS])->hasKeys('target');
    }

    #[\Testo\Test]
    public function testJtiChildDoesNotInheritRelationFromTrait(): void
    {
        $schema = $this->compileWithClasses([TrTarget::class, TrParent::class, TrJti::class]);

        // Trait-declared relation belongs to the entity that uses the trait
        \Testo\Assert::array($schema['trParent'][SchemaInterface::COLUMNS])->hasKeys('target_id');
        \Testo\Assert::count($schema['trParent'][SchemaInterface::RELATIONS], 1);

        // JTI child must not duplicate the trait's relation columns
        \Testo\Assert::array($schema['trJti'][SchemaInterface::COLUMNS])->doesNotHaveKeys('target_id');
        \Testo\Assert::count($schema['trJti'][SchemaInterface::RELATIONS] ?? [], 0);
    }

    #[\Testo\Test]
    public function testJtiGrandchildDoesNotInheritMidLevelRelation(): void
    {
        $schema = $this->compileWithClasses([
            JgTarget::class,
            JgParent::class,
            JgMid::class,
            JgLeaf::class,
        ]);

        // Relation declared on the JTI middle entity stays on the middle table
        \Testo\Assert::array($schema['jgMid'][SchemaInterface::COLUMNS])->hasKeys('target_id');
        \Testo\Assert::count($schema['jgMid'][SchemaInterface::RELATIONS], 1);

        // JTI grandchild must not duplicate the middle entity's relation column
        \Testo\Assert::array($schema['jgLeaf'][SchemaInterface::COLUMNS])->doesNotHaveKeys('target_id');
        \Testo\Assert::count($schema['jgLeaf'][SchemaInterface::RELATIONS] ?? [], 0);
    }

    /**
     * @param list<class-string> $classes
     */
    private function compileWithClasses(array $classes): array
    {
        $reader = new AttributeReader();
        $locator = $this->createMock(ClassesInterface::class);
        $locator->method('getClasses')->willReturn(
            \array_map(static fn(string $c) => new \ReflectionClass($c), $classes),
        );

        return (new Compiler())->compile(new Registry($this->dbal), [
            new Embeddings(new TokenizerEmbeddingLocator($locator, $reader), $reader),
            new Entities(new TokenizerEntityLocator($locator, $reader), $reader),
            new TableInheritance($reader),
            new ResetTables(),
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
