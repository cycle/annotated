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
    #[DataProvider('allReadersProvider')]
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
        $this->assertArrayHasKey('tool', $schema);

        // Person
        $this->assertCount(3, $schema['person'][SchemaInterface::CHILDREN]);
        $this->assertEquals([
            'employee' => Employee::class,
            'foo_customer' => Customer::class,
            'ceo' => Ceo::class,
        ], $schema['person'][SchemaInterface::CHILDREN]);
        $this->assertSame('type', $schema['person'][SchemaInterface::DISCRIMINATOR]);
        $this->assertEquals([
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
        ], $schema['person'][SchemaInterface::COLUMNS]);
        $this->assertEmpty($schema['person'][SchemaInterface::PARENT] ?? null);
        $this->assertEmpty($schema['person'][SchemaInterface::PARENT_KEY] ?? null);
        $this->assertSame('people', $schema['person'][SchemaInterface::TABLE]);
        $this->assertCount(1, $schema['person'][SchemaInterface::RELATIONS]);

        // Employee
        $this->assertArrayHasKey('employee', $schema);
        $this->assertCount(1, $schema['employee']);
        $this->assertSame(Employee::class, $schema['employee'][SchemaInterface::ENTITY]);
        $this->assertNull($schema['employee'][SchemaInterface::TABLE] ?? null);
        $this->assertCount(0, $schema['employee'][SchemaInterface::RELATIONS] ?? []);

        // Customer
        $this->assertArrayHasKey('customer', $schema);
        $this->assertCount(1, $schema['customer']);
        $this->assertSame(Customer::class, $schema['customer'][SchemaInterface::ENTITY]);
        $this->assertNull($schema['customer'][SchemaInterface::TABLE] ?? null);
        $this->assertCount(0, $schema['customer'][SchemaInterface::RELATIONS] ?? []);

        // Executive
        $this->assertSame('employee', $schema['executive'][SchemaInterface::PARENT]);
        $this->assertSame('foo_id', $schema['executive'][SchemaInterface::PARENT_KEY]);
        $this->assertSame('executives', $schema['executive'][SchemaInterface::TABLE]);
        $this->assertEquals(
            [
                'bonus' => 'bonus',
                'proxyFieldWithAnnotation' => 'proxy',
                'foo_id' => 'id',
                'hidden' => 'hidden',
                'added_tool_id' => 'added_tool_id',
            ],
            $schema['executive'][SchemaInterface::COLUMNS],
        );
        $this->assertCount(1, $schema['executive'][SchemaInterface::RELATIONS]);

        // Executive2
        $this->assertSame('executive', $schema['executive2'][SchemaInterface::PARENT]);
        $this->assertSame('foo_id', $schema['executive2'][SchemaInterface::PARENT_KEY]);
        $this->assertEquals(['foo_id' => 'id'], $schema['executive2'][SchemaInterface::COLUMNS]);
        $this->assertCount(0, $schema['executive2'][SchemaInterface::RELATIONS]);

        // Ceo
        $this->assertArrayHasKey('ceo', $schema);
        $this->assertCount(1, $schema['ceo']);
        $this->assertSame(Ceo::class, $schema['ceo'][SchemaInterface::ENTITY]);
        $this->assertNull($schema['ceo'][SchemaInterface::TABLE] ?? null);
        $this->assertCount(0, $schema['ceo'][SchemaInterface::RELATIONS] ?? []);

        // Beaver
        $this->assertEmpty($schema['beaver'][SchemaInterface::DISCRIMINATOR] ?? null);
        $this->assertEmpty($schema['beaver'][SchemaInterface::PARENT] ?? null);
        $this->assertEmpty($schema['beaver'][SchemaInterface::PARENT_KEY] ?? null);
        $this->assertEmpty($schema['beaver'][SchemaInterface::CHILDREN] ?? null);
        $this->assertSame('beavers', $schema['beaver'][SchemaInterface::TABLE]);
        $this->assertEquals([
            'teethAmount' => 'teeth_amount',
            'foo_id' => 'id',
            'name' => 'name',
            'type' => 'type',
            'hidden' => 'hidden',
            'tool_id' => 'tool_id',
        ], $schema['beaver'][SchemaInterface::COLUMNS]);
        $this->assertCount(1, $schema['beaver'][SchemaInterface::RELATIONS] ?? []);
    }

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

        $this->assertSame('executives', $schema['executive'][SchemaInterface::TABLE]);
        $this->assertNull($schema['employee'][SchemaInterface::TABLE] ?? null);
        $this->assertSame('people', $schema['person'][SchemaInterface::TABLE]);
    }

    public function testJtiChildDoesNotInheritBelongsToColumn(): void
    {
        $schema = $this->compileWithClasses([BtTarget::class, BtParent::class, BtJoined::class]);

        // BtParent owns the BelongsTo + its FK column
        $this->assertArrayHasKey('target_id', $schema['btParent'][SchemaInterface::COLUMNS]);
        $this->assertCount(1, $schema['btParent'][SchemaInterface::RELATIONS]);

        // JTI child must not duplicate the BelongsTo FK column nor inherit the relation
        $this->assertArrayNotHasKey('target_id', $schema['btJoined'][SchemaInterface::COLUMNS]);
        $this->assertCount(0, $schema['btJoined'][SchemaInterface::RELATIONS] ?? []);
    }

    public function testStiChildOwnRelationMergesIntoParent(): void
    {
        $schema = $this->compileWithClasses([StTarget::class, StParent::class, StChild::class]);

        // Relation declared on the STI child must end up on the parent after merge
        $this->assertArrayHasKey('target_id', $schema['stParent'][SchemaInterface::COLUMNS]);
        $this->assertCount(1, $schema['stParent'][SchemaInterface::RELATIONS]);
        $this->assertArrayHasKey('target', $schema['stParent'][SchemaInterface::RELATIONS]);
    }

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
        $this->assertNotNull($schema['soSeparate'][SchemaInterface::TABLE] ?? null);
        $this->assertArrayHasKey('target_id', $schema['soSeparate'][SchemaInterface::COLUMNS]);
        $this->assertArrayHasKey('extra', $schema['soSeparate'][SchemaInterface::COLUMNS]);
        $this->assertCount(1, $schema['soSeparate'][SchemaInterface::RELATIONS] ?? []);
        $this->assertArrayHasKey('target', $schema['soSeparate'][SchemaInterface::RELATIONS]);
    }

    public function testJtiChildDoesNotInheritRelationFromTrait(): void
    {
        $schema = $this->compileWithClasses([TrTarget::class, TrParent::class, TrJti::class]);

        // Trait-declared relation belongs to the entity that uses the trait
        $this->assertArrayHasKey('target_id', $schema['trParent'][SchemaInterface::COLUMNS]);
        $this->assertCount(1, $schema['trParent'][SchemaInterface::RELATIONS]);

        // JTI child must not duplicate the trait's relation columns
        $this->assertArrayNotHasKey('target_id', $schema['trJti'][SchemaInterface::COLUMNS]);
        $this->assertCount(0, $schema['trJti'][SchemaInterface::RELATIONS] ?? []);
    }

    public function testJtiGrandchildDoesNotInheritMidLevelRelation(): void
    {
        $schema = $this->compileWithClasses([
            JgTarget::class,
            JgParent::class,
            JgMid::class,
            JgLeaf::class,
        ]);

        // Relation declared on the JTI middle entity stays on the middle table
        $this->assertArrayHasKey('target_id', $schema['jgMid'][SchemaInterface::COLUMNS]);
        $this->assertCount(1, $schema['jgMid'][SchemaInterface::RELATIONS]);

        // JTI grandchild must not duplicate the middle entity's relation column
        $this->assertArrayNotHasKey('target_id', $schema['jgLeaf'][SchemaInterface::COLUMNS]);
        $this->assertCount(0, $schema['jgLeaf'][SchemaInterface::RELATIONS] ?? []);
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
