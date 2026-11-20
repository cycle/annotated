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
use Cycle\Annotated\Tests\Fixtures\Fixtures16\Ceo;
use Cycle\Annotated\Tests\Fixtures\Fixtures16\Customer;
use Cycle\Annotated\Tests\Fixtures\Fixtures16\Employee;
use Cycle\Annotated\Tests\Fixtures\Fixtures16\Person;
use Cycle\Annotated\Tests\Functional\Driver\Common\BaseTestCase;
use Cycle\Annotated\Tests\Traits\TableTrait;
use Cycle\ORM\EntityManager;
use Cycle\ORM\Mapper\Mapper;
use Cycle\ORM\Relation;
use Cycle\ORM\Schema;
use Cycle\ORM\Schema\GeneratedField;
use Cycle\ORM\SchemaInterface;
use Cycle\ORM\Select\Repository;
use Cycle\ORM\Select\Source;
use Cycle\Schema\Compiler;
use Cycle\Schema\Generator\GenerateRelations;
use Cycle\Schema\Generator\GenerateTypecast;
use Cycle\Schema\Generator\RenderRelations;
use Cycle\Schema\Generator\RenderTables;
use Cycle\Schema\Generator\ResetTables;
use Cycle\Schema\Generator\SyncTables;
use Cycle\Schema\Registry;
use PHPUnit\Framework\Attributes\DataProvider;
use Spiral\Attributes\ReaderInterface;
use Spiral\Tokenizer\Config\TokenizerConfig;
use Spiral\Tokenizer\Tokenizer;

abstract class SingleTableTestCase extends BaseTestCase
{
    use TableTrait;

    public static function columnDeclarationDataProvider(): \Traversable
    {
        // Declaration via Column in the property
        yield [__DIR__ . '/../../../../Fixtures/Fixtures23/STIWithPropertyColumn', 'STIWithPropertyColumn'];

        // Declaration via Column in the class
        yield [__DIR__ . '/../../../../Fixtures/Fixtures23/STIWithClassColumn', 'STIWithClassColumn'];

        // Declaration via Table in the class
        yield [__DIR__ . '/../../../../Fixtures/Fixtures23/STIWithTableColumn', 'STIWithTableColumn'];

        // Declaration via columns in the Entity
        yield [__DIR__ . '/../../../../Fixtures/Fixtures23/STIWithEntityColumn', 'STIWithEntityColumn'];
    }

    /**
     * Must return 12 elements - 4 declarations x 3 readers.
     */
    public static function columnDeclarationWithReadersDataProvider(): \Traversable
    {
        $readers = \iterator_to_array(static::allReadersProvider(), false);
        $declarations = \iterator_to_array(static::columnDeclarationDataProvider());
        foreach ($readers as $reader) {
            foreach ($declarations as $declaration) {
                yield \array_merge($declaration, $reader);
            }
        }
    }

    #[\Testo\Data\DataProvider('allReadersProvider')]
    #[\Testo\Test]
    public function testTableInheritance(ReaderInterface $reader): void
    {
        $tokenizer = new Tokenizer(
            new TokenizerConfig([
                'directories' => [__DIR__ . '/../../../../Fixtures/Fixtures16'],
                'exclude' => [],
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

        \Testo\Assert::notNull($person->getFooId());
        $loadedPerson = $this->orm->getRepository(Person::class)->findByPK($person->getFooId());
        \Testo\Assert::instanceOf($loadedPerson, Person::class);
        \Testo\Assert::same($loadedPerson->name, 'baz');
        \Testo\Assert::false(isset($loadedPerson->preferences));
        \Testo\Assert::false(isset($loadedPerson->salary));

        \Testo\Assert::notNull($employee->getFooId());
        $loadedEmployee = $this->orm->getRepository(Person::class)->findByPK($employee->getFooId());
        \Testo\Assert::instanceOf($loadedEmployee, Employee::class);
        \Testo\Assert::same($loadedEmployee->name, 'foo');
        \Testo\Assert::same($loadedEmployee->salary, 12500);
        \Testo\Assert::same($loadedEmployee->bar, 'test');
        \Testo\Assert::false(isset($loadedEmployee->preferences));

        \Testo\Assert::notNull($customer->getFooId());
        $loadedCustomer = $this->orm->getRepository(Person::class)->findByPK($customer->getFooId());
        \Testo\Assert::instanceOf($loadedCustomer, Customer::class);
        \Testo\Assert::same($loadedCustomer->name, 'baz');
        \Testo\Assert::same($loadedCustomer->preferences, 'private');
        \Testo\Assert::false(isset($loadedCustomer->salary));

        \Testo\Assert::notNull($ceo->getFooId());
        $loadedCeo = $this->orm->getRepository(Ceo::class)->findByPK($ceo->getFooId());
        \Testo\Assert::instanceOf($loadedCeo, Ceo::class);
        \Testo\Assert::same($loadedCeo->name, 'ceo');
        \Testo\Assert::same($loadedCeo->salary, 150000);
        \Testo\Assert::same($loadedCeo->stocks, 1000);
    }

    #[\Testo\Data\DataProvider('allReadersProvider')]
    #[\Testo\Test]
    public function testTableInheritanceSchema(ReaderInterface $reader): void
    {
        $tokenizer = new Tokenizer(
            new TokenizerConfig([
                'directories' => [__DIR__ . '/../../../../Fixtures/Fixtures20'],
                'exclude' => [],
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
            new GenerateTypecast(),
        ]);

        \Testo\Assert::notBlank($schema);

        \Testo\Assert::array($schema['person'])->hasKeys(SchemaInterface::CHILDREN);
        \Testo\Assert::array($schema['person'])->hasKeys(SchemaInterface::DISCRIMINATOR);
        \Testo\Assert::same($schema['person'][SchemaInterface::DISCRIMINATOR], 'type');

        \Testo\Assert::array($schema['buyer'])->hasKeys(SchemaInterface::PARENT);
        \Testo\Assert::array($schema['buyer'])->hasKeys(SchemaInterface::PARENT_KEY);
    }

    #[\Testo\Data\DataProvider('columnDeclarationWithReadersDataProvider')]
    #[\Testo\Test]
    public function testSingleTableInheritanceWithDifferentColumnDeclaration(
        string $directory,
        string $namespace,
        ReaderInterface $reader,
    ): void {
        $tokenizer = new Tokenizer(
            new TokenizerConfig([
                'directories' => [$directory],
                'exclude' => [],
            ]),
        );

        $locator = $tokenizer->classLocator();

        $r = new Registry($this->dbal);

        $schema = (new Compiler())->compile($r, [
            new Embeddings(new TokenizerEmbeddingLocator($locator, $reader), $reader),
            new Entities(new TokenizerEntityLocator($locator, $reader), $reader),
            new MergeColumns($reader),
            new TableInheritance($reader),
            new ResetTables(),
            new GenerateRelations(),
            new RenderTables(),
            new RenderRelations(),
            new MergeIndexes($reader),
            new GenerateTypecast(),
        ]);

        \Testo\Assert::notBlank($schema);
        \Testo\Assert::same($schema['baseEvent'][SchemaInterface::ENTITY], \sprintf(
            'Cycle\Annotated\Tests\Fixtures\Fixtures23\%s\BaseEvent',
            $namespace,
        ));
        \Testo\Assert::same($schema['baseEvent'][SchemaInterface::MAPPER], Mapper::class);
        \Testo\Assert::same($schema['baseEvent'][SchemaInterface::SOURCE], Source::class);
        \Testo\Assert::same($schema['baseEvent'][SchemaInterface::REPOSITORY], Repository::class);
        \Testo\Assert::same($schema['baseEvent'][SchemaInterface::DATABASE], 'default');
        \Testo\Assert::same($schema['baseEvent'][SchemaInterface::TABLE], 'base_events');
        \Testo\Assert::same($schema['baseEvent'][SchemaInterface::PRIMARY_KEY], ['id']);
        \Testo\Assert::same($schema['baseEvent'][SchemaInterface::FIND_BY_KEYS], ['id']);
        \Testo\Assert::count($schema['baseEvent'][SchemaInterface::COLUMNS], 4);
        \Testo\Assert::same($schema['baseEvent'][SchemaInterface::COLUMNS]['id'], 'id');
        \Testo\Assert::same($schema['baseEvent'][SchemaInterface::COLUMNS]['action'], 'action');
        \Testo\Assert::same($schema['baseEvent'][SchemaInterface::COLUMNS]['object_id'], 'object_id');
        \Testo\Assert::same($schema['baseEvent'][SchemaInterface::COLUMNS]['object_type'], 'object_type');
        \Testo\Assert::same($schema['baseEvent'][SchemaInterface::RELATIONS]['object'][Relation::TYPE], Relation::BELONGS_TO_MORPHED);
        \Testo\Assert::same($schema['baseEvent'][SchemaInterface::RELATIONS]['object'][Relation::TARGET], \sprintf('Cycle\Annotated\Tests\Fixtures\Fixtures23\%s\EventEmitterInterface', $namespace));
        \Testo\Assert::same($schema['baseEvent'][SchemaInterface::RELATIONS]['object'][Relation::LOAD], Relation::LOAD_PROMISE);
        \Testo\Assert::true($schema['baseEvent'][SchemaInterface::RELATIONS]['object'][Relation::SCHEMA][Relation::CASCADE]);
        \Testo\Assert::true($schema['baseEvent'][SchemaInterface::RELATIONS]['object'][Relation::SCHEMA][Relation::NULLABLE]);
        \Testo\Assert::same($schema['baseEvent'][SchemaInterface::RELATIONS]['object'][Relation::SCHEMA][Relation::OUTER_KEY], ['id']);
        \Testo\Assert::same($schema['baseEvent'][SchemaInterface::RELATIONS]['object'][Relation::SCHEMA][Relation::INNER_KEY], 'object_id');
        \Testo\Assert::same($schema['baseEvent'][SchemaInterface::RELATIONS]['object'][Relation::SCHEMA][Relation::MORPH_KEY], 'object_type');
        \Testo\Assert::count($schema['baseEvent'][SchemaInterface::CHILDREN], 2);
        \Testo\Assert::same($schema['baseEvent'][SchemaInterface::CHILDREN]['comment.created'], \sprintf('Cycle\Annotated\Tests\Fixtures\Fixtures23\%s\CommentCreated', $namespace));
        \Testo\Assert::same($schema['baseEvent'][SchemaInterface::CHILDREN]['comment.updated'], \sprintf('Cycle\Annotated\Tests\Fixtures\Fixtures23\%s\CommentUpdated', $namespace));
        \Testo\Assert::null($schema['baseEvent'][SchemaInterface::SCOPE]);
        \Testo\Assert::same($schema['baseEvent'][SchemaInterface::TYPECAST]['id'], 'int');
        \Testo\Assert::same($schema['baseEvent'][SchemaInterface::TYPECAST]['object_id'], 'int');
        \Testo\Assert::same($schema['baseEvent'][SchemaInterface::SCHEMA], []);
        \Testo\Assert::same($schema['baseEvent'][SchemaInterface::DISCRIMINATOR], 'action');
        \Testo\Assert::null($schema['baseEvent'][SchemaInterface::TYPECAST_HANDLER]);
        \Testo\Assert::same($schema['comment'], [
            SchemaInterface::ENTITY => \sprintf(
                'Cycle\Annotated\Tests\Fixtures\Fixtures23\%s\Comment',
                $namespace,
            ),
            SchemaInterface::MAPPER => Mapper::class,
            SchemaInterface::SOURCE => Source::class,
            SchemaInterface::REPOSITORY => Repository::class,
            SchemaInterface::DATABASE => 'default',
            SchemaInterface::TABLE => 'comments',
            SchemaInterface::PRIMARY_KEY => [
                'id',
            ],
            SchemaInterface::FIND_BY_KEYS => [
                'id',
            ],
            SchemaInterface::COLUMNS => [
                'id' => 'id',
                'body' => 'body',
            ],
            SchemaInterface::RELATIONS => [],
            SchemaInterface::SCOPE => null,
            SchemaInterface::TYPECAST => [
                'id' => 'int',
                'body' => 'string',
            ],
            SchemaInterface::SCHEMA => [],
            SchemaInterface::TYPECAST_HANDLER => null,
            SchemaInterface::GENERATED_FIELDS => [
                'id' => GeneratedField::ON_INSERT,
            ],
        ]);
        \Testo\Assert::same($schema['commentCreated'], [
            SchemaInterface::ENTITY => \sprintf(
                'Cycle\Annotated\Tests\Fixtures\Fixtures23\%s\CommentCreated',
                $namespace,
            ),
        ]);
        \Testo\Assert::same($schema['commentUpdated'], [
            SchemaInterface::ENTITY => \sprintf(
                'Cycle\Annotated\Tests\Fixtures\Fixtures23\%s\CommentUpdated',
                $namespace,
            ),
        ]);
    }

    #[\Testo\Lifecycle\BeforeTest]
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
}
