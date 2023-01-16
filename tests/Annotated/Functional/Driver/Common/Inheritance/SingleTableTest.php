<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Functional\Driver\Common\Inheritance;

use Cycle\Annotated\Embeddings;
use Cycle\Annotated\Entities;
use Cycle\Annotated\Locator\TokenizerEmbeddingLocator;
use Cycle\Annotated\Locator\TokenizerEntityLocator;
use Cycle\Annotated\MergeColumns;
use Cycle\Annotated\MergeIndexes;
use Cycle\Annotated\TableInheritance;
use Cycle\Annotated\Tests\Fixtures\Fixtures16\Customer;
use Cycle\Annotated\Tests\Fixtures\Fixtures16\Employee;
use Cycle\Annotated\Tests\Fixtures\Fixtures16\Person;
use Cycle\Annotated\Tests\Fixtures\Fixtures16\Ceo;
use Cycle\Annotated\Tests\Functional\Driver\Common\BaseTest;
use Cycle\Annotated\Tests\Traits\TableTrait;
use Cycle\ORM\Schema;
use Cycle\ORM\SchemaInterface;
use Cycle\ORM\EntityManager;
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

abstract class SingleTableTest extends BaseTest
{
    use TableTrait;

    public function setUp(): void
    {
        parent::setUp();

        $this->makeTable('people', [
            'id' => 'primary',
            'type' => 'string',
            'name' => 'string',
            'salary' => 'float',
            'preferences' => 'string',
            'bar' => 'string',
            'stocks' => 'int',
        ]);
    }

    /**
     * @dataProvider allReadersProvider
     */
    public function testTableInheritance(ReaderInterface $reader): void
    {
        $tokenizer = new Tokenizer(
            new TokenizerConfig([
                'directories' => [__DIR__ . '/../../../../Fixtures/Fixtures16'],
                'exclude' => [],
            ])
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

        $this->orm = $this->orm->with(new Schema($schema));

        $t = new EntityManager($this->orm);

        $employee = new Employee();
        $employee->name = 'foo';
        $employee->salary = 12500;
        $employee->bar = 'test';

        $employee1 = new Employee();
        $employee1->name = 'bar';
        $employee1->salary = 23000;

        $person = new Person();
        $person->name = 'baz';

        $customer = new Customer();
        $customer->name = 'baz';
        $customer->preferences = 'private';

        $ceo = new Ceo();
        $ceo->name = 'ceo';
        $ceo->stocks = 1000;
        $ceo->salary = 150000;

        $t->persist($employee);
        $t->persist($employee1);
        $t->persist($person);
        $t->persist($customer);
        $t->persist($ceo);

        $t->run();

        $this->orm->getHeap()->clean();

        $this->assertNotNull($person->getFooId());
        $loadedPerson = $this->orm->getRepository(Person::class)->findByPK($person->getFooId());
        $this->assertInstanceOf(Person::class, $loadedPerson);
        $this->assertSame('baz', $loadedPerson->name);
        $this->assertFalse(isset($loadedPerson->preferences));
        $this->assertFalse(isset($loadedPerson->salary));

        $this->assertNotNull($employee->getFooId());
        $loadedEmployee = $this->orm->getRepository(Person::class)->findByPK($employee->getFooId());
        $this->assertInstanceOf(Employee::class, $loadedEmployee);
        $this->assertSame('foo', $loadedEmployee->name);
        $this->assertSame(12500, $loadedEmployee->salary);
        $this->assertSame('test', $loadedEmployee->bar);
        $this->assertFalse(isset($loadedEmployee->preferences));

        $this->assertNotNull($customer->getFooId());
        $loadedCustomer = $this->orm->getRepository(Person::class)->findByPK($customer->getFooId());
        $this->assertInstanceOf(Customer::class, $loadedCustomer);
        $this->assertSame('baz', $loadedCustomer->name);
        $this->assertSame('private', $loadedCustomer->preferences);
        $this->assertFalse(isset($loadedCustomer->salary));

        $this->assertNotNull($ceo->getFooId());
        $loadedCeo = $this->orm->getRepository(Ceo::class)->findByPK($ceo->getFooId());
        $this->assertInstanceOf(Ceo::class, $loadedCeo);
        $this->assertSame('ceo', $loadedCeo->name);
        $this->assertSame(150000, $loadedCeo->salary);
        $this->assertSame(1000, $loadedCeo->stocks);
    }

    /**
     * @dataProvider allReadersProvider
     */
    public function testTableInheritanceSchema(ReaderInterface $reader): void
    {
        $tokenizer = new Tokenizer(
            new TokenizerConfig([
                'directories' => [__DIR__ . '/../../../../Fixtures/Fixtures20'],
                'exclude' => [],
            ])
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
            new GenerateTypecast(),
        ]);

        $this->assertNotEmpty($schema);

        $this->assertArrayHasKey(SchemaInterface::CHILDREN, $schema['person']);
        $this->assertArrayHasKey(SchemaInterface::DISCRIMINATOR, $schema['person']);
        $this->assertSame('type', $schema['person'][SchemaInterface::DISCRIMINATOR]);

        $this->assertArrayHasKey(SchemaInterface::PARENT, $schema['buyer']);
        $this->assertArrayHasKey(SchemaInterface::PARENT_KEY, $schema['buyer']);
    }
}
