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
}
