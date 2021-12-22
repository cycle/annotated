<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Functional\Driver\Common;

use Cycle\Annotated\Embeddings;
use Cycle\Annotated\Entities;
use Cycle\Annotated\MergeColumns;
use Cycle\Annotated\MergeIndexes;
use Cycle\Annotated\TableInheritance;
use Cycle\Annotated\Tests\Fixtures\Fixtures16\Ceo;
use Cycle\Annotated\Tests\Fixtures\Fixtures16\Customer;
use Cycle\Annotated\Tests\Fixtures\Fixtures16\Employee;
use Cycle\ORM\SchemaInterface;
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

abstract class InheritanceTest extends BaseTest
{
    /**
     * @dataProvider allReadersProvider
     */
    public function testTableInheritance(ReaderInterface $reader): void
    {
        $tokenizer = new Tokenizer(
            new TokenizerConfig([
                'directories' => [__DIR__ . '/../../../Fixtures/Fixtures16'],
                'exclude' => [__DIR__ . '/Fixtures16/CatWithoutParent.php'],
            ])
        );

        $locator = $tokenizer->classLocator();

        $r = new Registry($this->dbal);

        $schema = (new Compiler())->compile($r, [
            new Embeddings($locator, $reader),
            new Entities($locator, $reader),
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
            // 'teethAmount' => 'teeth_amount', // Not child
        ], $schema['person'][SchemaInterface::COLUMNS]);
        $this->assertEmpty($schema['person'][SchemaInterface::PARENT] ?? null);
        $this->assertEmpty($schema['person'][SchemaInterface::PARENT_KEY] ?? null);
        $this->assertSame('people', $schema['person'][SchemaInterface::TABLE]);

        // Employee
        $this->assertArrayHasKey('employee', $schema);
        $this->assertCount(1, $schema['employee']);
        $this->assertSame(Employee::class, $schema['employee'][SchemaInterface::ENTITY]);
        $this->assertNull($schema['employee'][SchemaInterface::TABLE] ?? null);

        // Customer
        $this->assertArrayHasKey('customer', $schema);
        $this->assertCount(1, $schema['customer']);
        $this->assertSame(Customer::class, $schema['customer'][SchemaInterface::ENTITY]);
        $this->assertNull($schema['customer'][SchemaInterface::TABLE] ?? null);

        // Executive
        $this->assertSame('employee', $schema['executive'][SchemaInterface::PARENT]);
        $this->assertSame('foo_id', $schema['executive'][SchemaInterface::PARENT_KEY]);
        $this->assertSame('executives', $schema['executive'][SchemaInterface::TABLE]);
        $this->assertSame(
            ['bonus' => 'bonus', 'foo_id' => 'id', 'hidden' => 'hidden'],
            $schema['executive'][SchemaInterface::COLUMNS]
        );

        // Ceo
        $this->assertArrayHasKey('ceo', $schema);
        $this->assertCount(1, $schema['ceo']);
        $this->assertSame(Ceo::class, $schema['ceo'][SchemaInterface::ENTITY]);
        $this->assertNull($schema['ceo'][SchemaInterface::TABLE] ?? null);

        // Beaver
        $this->assertEmpty($schema['beaver'][SchemaInterface::DISCRIMINATOR] ?? null);
        $this->assertEmpty($schema['beaver'][SchemaInterface::PARENT] ?? null);
        $this->assertEmpty($schema['beaver'][SchemaInterface::PARENT_KEY] ?? null);
        $this->assertEmpty($schema['beaver'][SchemaInterface::CHILDREN] ?? null);
        $this->assertSame('beavers', $schema['beaver'][SchemaInterface::TABLE]);
        $this->assertSame([
            'teethAmount' => 'teeth_amount',
            'foo_id' => 'id',
            'name' => 'name',
            'type' => 'type',
            'hidden' => 'hidden',
        ], $schema['beaver'][SchemaInterface::COLUMNS]);
    }
}
