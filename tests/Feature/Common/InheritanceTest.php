<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Feature\Common;

use Cycle\Annotated\Embeddings;
use Cycle\Annotated\Entities;
use Cycle\Annotated\Locator\TokenizerEmbeddingLocator;
use Cycle\Annotated\Locator\TokenizerEntityLocator;
use Cycle\Annotated\MergeColumns;
use Cycle\Annotated\MergeIndexes;
use Cycle\Annotated\TableInheritance;
use Cycle\Annotated\Tests\Feature\BaseTestCase;
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
use Cycle\Testing\ORM\Schema\ClassList;
use Spiral\Attributes\AttributeReader;
use Spiral\Attributes\ReaderInterface;
use Spiral\Tokenizer\Config\TokenizerConfig;
use Spiral\Tokenizer\Tokenizer;
use Testo\Assert;
use Testo\Data\DataProvider;
use Testo\Test;

final class InheritanceTest extends BaseTestCase
{
    #[DataProvider('allReadersProvider')]
    #[Test]
    public function testTableInheritance(ReaderInterface $reader): void
    {
        $tokenizer = new Tokenizer(
            new TokenizerConfig([
                'directories' => [__DIR__ . '/../../Fixtures/Fixtures16'],
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
        Assert::array($schema)->hasKeys('tool');

        // Person
        Assert::count($schema['person'][SchemaInterface::CHILDREN], 3);
        Assert::equals($schema['person'][SchemaInterface::CHILDREN], [
            'employee' => Employee::class,
            'foo_customer' => Customer::class,
            'ceo' => Ceo::class,
        ]);
        Assert::same($schema['person'][SchemaInterface::DISCRIMINATOR], 'type');
        Assert::equals($schema['person'][SchemaInterface::COLUMNS], [
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
        Assert::blank($schema['person'][SchemaInterface::PARENT] ?? null);
        Assert::blank($schema['person'][SchemaInterface::PARENT_KEY] ?? null);
        Assert::same($schema['person'][SchemaInterface::TABLE], 'people');
        Assert::count($schema['person'][SchemaInterface::RELATIONS], 1);

        // Employee
        Assert::array($schema)->hasKeys('employee');
        Assert::count($schema['employee'], 1);
        Assert::same($schema['employee'][SchemaInterface::ENTITY], Employee::class);
        Assert::null($schema['employee'][SchemaInterface::TABLE] ?? null);
        Assert::count($schema['employee'][SchemaInterface::RELATIONS] ?? [], 0);

        // Customer
        Assert::array($schema)->hasKeys('customer');
        Assert::count($schema['customer'], 1);
        Assert::same($schema['customer'][SchemaInterface::ENTITY], Customer::class);
        Assert::null($schema['customer'][SchemaInterface::TABLE] ?? null);
        Assert::count($schema['customer'][SchemaInterface::RELATIONS] ?? [], 0);

        // Executive
        Assert::same($schema['executive'][SchemaInterface::PARENT], 'employee');
        Assert::same($schema['executive'][SchemaInterface::PARENT_KEY], 'foo_id');
        Assert::same($schema['executive'][SchemaInterface::TABLE], 'executives');
        Assert::equals($schema['executive'][SchemaInterface::COLUMNS], [
            'bonus' => 'bonus',
            'proxyFieldWithAnnotation' => 'proxy',
            'foo_id' => 'id',
            'hidden' => 'hidden',
            'added_tool_id' => 'added_tool_id',
        ]);
        Assert::count($schema['executive'][SchemaInterface::RELATIONS], 1);

        // Executive2
        Assert::same($schema['executive2'][SchemaInterface::PARENT], 'executive');
        Assert::same($schema['executive2'][SchemaInterface::PARENT_KEY], 'foo_id');
        Assert::equals($schema['executive2'][SchemaInterface::COLUMNS], ['foo_id' => 'id']);
        Assert::count($schema['executive2'][SchemaInterface::RELATIONS], 0);

        // Ceo
        Assert::array($schema)->hasKeys('ceo');
        Assert::count($schema['ceo'], 1);
        Assert::same($schema['ceo'][SchemaInterface::ENTITY], Ceo::class);
        Assert::null($schema['ceo'][SchemaInterface::TABLE] ?? null);
        Assert::count($schema['ceo'][SchemaInterface::RELATIONS] ?? [], 0);

        // Beaver
        Assert::blank($schema['beaver'][SchemaInterface::DISCRIMINATOR] ?? null);
        Assert::blank($schema['beaver'][SchemaInterface::PARENT] ?? null);
        Assert::blank($schema['beaver'][SchemaInterface::PARENT_KEY] ?? null);
        Assert::blank($schema['beaver'][SchemaInterface::CHILDREN] ?? null);
        Assert::same($schema['beaver'][SchemaInterface::TABLE], 'beavers');
        Assert::equals($schema['beaver'][SchemaInterface::COLUMNS], [
            'teethAmount' => 'teeth_amount',
            'foo_id' => 'id',
            'name' => 'name',
            'type' => 'type',
            'hidden' => 'hidden',
            'tool_id' => 'tool_id',
        ]);
        Assert::count($schema['beaver'][SchemaInterface::RELATIONS] ?? [], 1);
    }

    #[Test]
    public function testTableInheritanceWithIncorrectClassesOrder(): void
    {
        $r = new Registry($this->dbal);
        $reader = new AttributeReader();
        $locator = new ClassList(Employee::class, Executive::class, Person::class, Tool::class);

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

        Assert::same($schema['executive'][SchemaInterface::TABLE], 'executives');
        Assert::null($schema['employee'][SchemaInterface::TABLE] ?? null);
        Assert::same($schema['person'][SchemaInterface::TABLE], 'people');
    }

    #[Test]
    public function testJtiChildDoesNotInheritBelongsToColumn(): void
    {
        $schema = $this->compileWithClasses([BtTarget::class, BtParent::class, BtJoined::class]);

        // BtParent owns the BelongsTo + its FK column
        Assert::array($schema['btParent'][SchemaInterface::COLUMNS])->hasKeys('target_id');
        Assert::count($schema['btParent'][SchemaInterface::RELATIONS], 1);

        // JTI child must not duplicate the BelongsTo FK column nor inherit the relation
        Assert::array($schema['btJoined'][SchemaInterface::COLUMNS])->doesNotHaveKeys('target_id');
        Assert::count($schema['btJoined'][SchemaInterface::RELATIONS] ?? [], 0);
    }

    #[Test]
    public function testStiChildOwnRelationMergesIntoParent(): void
    {
        $schema = $this->compileWithClasses([StTarget::class, StParent::class, StChild::class]);

        // Relation declared on the STI child must end up on the parent after merge
        Assert::array($schema['stParent'][SchemaInterface::COLUMNS])->hasKeys('target_id');
        Assert::count($schema['stParent'][SchemaInterface::RELATIONS], 1);
        Assert::array($schema['stParent'][SchemaInterface::RELATIONS])->hasKeys('target');
    }

    #[Test]
    public function testSeparateEntityExtendingStiChildKeepsInheritedRelation(): void
    {
        $schema = $this->compileWithClasses([
            SoTarget::class,
            SoParent::class,
            SoSti::class,
            SoSeparate::class,
        ]);

        // Separate entity (no Inheritance attr) extending an STI child must keep ancestor relations
        Assert::blank($schema['soSeparate'][SchemaInterface::PARENT] ?? null);
        Assert::notNull($schema['soSeparate'][SchemaInterface::TABLE] ?? null);
        Assert::array($schema['soSeparate'][SchemaInterface::COLUMNS])->hasKeys('target_id');
        Assert::array($schema['soSeparate'][SchemaInterface::COLUMNS])->hasKeys('extra');
        Assert::count($schema['soSeparate'][SchemaInterface::RELATIONS] ?? [], 1);
        Assert::array($schema['soSeparate'][SchemaInterface::RELATIONS])->hasKeys('target');
    }

    #[Test]
    public function testJtiChildDoesNotInheritRelationFromTrait(): void
    {
        $schema = $this->compileWithClasses([TrTarget::class, TrParent::class, TrJti::class]);

        // Trait-declared relation belongs to the entity that uses the trait
        Assert::array($schema['trParent'][SchemaInterface::COLUMNS])->hasKeys('target_id');
        Assert::count($schema['trParent'][SchemaInterface::RELATIONS], 1);

        // JTI child must not duplicate the trait's relation columns
        Assert::array($schema['trJti'][SchemaInterface::COLUMNS])->doesNotHaveKeys('target_id');
        Assert::count($schema['trJti'][SchemaInterface::RELATIONS] ?? [], 0);
    }

    #[Test]
    public function testJtiGrandchildDoesNotInheritMidLevelRelation(): void
    {
        $schema = $this->compileWithClasses([
            JgTarget::class,
            JgParent::class,
            JgMid::class,
            JgLeaf::class,
        ]);

        // Relation declared on the JTI middle entity stays on the middle table
        Assert::array($schema['jgMid'][SchemaInterface::COLUMNS])->hasKeys('target_id');
        Assert::count($schema['jgMid'][SchemaInterface::RELATIONS], 1);

        // JTI grandchild must not duplicate the middle entity's relation column
        Assert::array($schema['jgLeaf'][SchemaInterface::COLUMNS])->doesNotHaveKeys('target_id');
        Assert::count($schema['jgLeaf'][SchemaInterface::RELATIONS] ?? [], 0);
    }

    /**
     * @param list<class-string> $classes
     */
    private function compileWithClasses(array $classes): array
    {
        $reader = new AttributeReader();
        $locator = new ClassList(...$classes);

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
