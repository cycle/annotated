<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests;

use Cycle\Annotated\Embeddings;
use Cycle\Annotated\Entities;
use Cycle\Annotated\MergeColumns;
use Cycle\Annotated\MergeIndexes;
use Cycle\Annotated\TableInheritance;
use Cycle\Annotated\Tests\Fixtures16\Customer;
use Cycle\Annotated\Tests\Fixtures16\Employee;
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

class InheritanceTest extends BaseTest
{
    public const DRIVER = 'sqlite';

    /**
     * @dataProvider allReadersProvider
     */
    public function testTableInheritance(ReaderInterface $reader): void
    {
        $tokenizer = new Tokenizer(
            new TokenizerConfig([
                'directories' => [__DIR__ . '/Fixtures16'],
                'exclude' => [__DIR__ . '/Fixtures16/CatWithoutParent.php'],
            ])
        );

        $locator = $tokenizer->classLocator();

        $r = new Registry($this->dbal);

        $schema = (new Compiler())->compile($r, [
            new Embeddings($locator, $reader),
            new Entities($locator, $reader),
            new TableInheritance($locator, $reader),
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
        // Beaver - Separate table

        // Person
        $this->assertCount(2, $schema['person'][SchemaInterface::CHILDREN]);
        $this->assertSame([
            'employee' => 'employee',
            'foo_customer' => 'customer',
        ], $schema['person'][SchemaInterface::CHILDREN]);
        $this->assertSame('type', $schema['person'][SchemaInterface::DISCRIMINATOR]);
        $this->assertSame([
            'id' => 'id',
            'name' => 'name',
            'salary' => 'salary',
            'bar' => 'bar',
            //'bonus' => 'bonus', // JTI
            'preferences' => 'preferences',
            //'teethAmount' => 'teeth_amount', // Not child
        ], $schema['person'][SchemaInterface::COLUMNS]);
        $this->assertEmpty($schema['person'][SchemaInterface::PARENT] ?? null);
        $this->assertEmpty($schema['person'][SchemaInterface::PARENT_KEY] ?? null);

        // Employee
        $this->assertArrayHasKey('employee', $schema);
        $this->assertCount(1, $schema['employee']);
        $this->assertSame(Employee::class, $schema['employee'][SchemaInterface::ENTITY]);

        // Customer
        $this->assertArrayHasKey('customer', $schema);
        $this->assertCount(1, $schema['customer']);
        $this->assertSame(Customer::class, $schema['customer'][SchemaInterface::ENTITY]);

        // Executive
        $this->assertSame('employee', $schema['executive'][SchemaInterface::PARENT]);
        $this->assertSame('foo_id', $schema['executive'][SchemaInterface::PARENT_KEY]);
        $this->assertSame(
            ['bonus' => 'bonus', 'id' => 'id', 'hidden' => 'hidden'],
            $schema['executive'][SchemaInterface::COLUMNS]
        );

        // Beaver
        $this->assertEmpty($schema['beaver'][SchemaInterface::DISCRIMINATOR] ?? null);
        $this->assertEmpty($schema['beaver'][SchemaInterface::PARENT] ?? null);
        $this->assertEmpty($schema['beaver'][SchemaInterface::PARENT_KEY] ?? null);
        $this->assertEmpty($schema['beaver'][SchemaInterface::CHILDREN] ?? null);
        $this->assertSame([
            'teethAmount' => 'teeth_amount',
            'id' => 'id',
            'name' => 'name',
            'hidden' => 'hidden',
        ], $schema['beaver'][SchemaInterface::COLUMNS]);
    }
}
