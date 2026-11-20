<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Feature\Common\Inheritance;

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
use Cycle\Annotated\Tests\Fixtures\Fixtures16\Person;
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
use Spiral\Attributes\ReaderInterface;
use Spiral\Tokenizer\Config\TokenizerConfig;
use Spiral\Tokenizer\Tokenizer;
use Testo\Assert;
use Testo\Data\DataProvider;
use Testo\Lifecycle\BeforeTest;
use Testo\Test;

final class SingleTableTest extends BaseTestCase
{
    public static function columnDeclarationDataProvider(): \Traversable
    {
        // Declaration via Column in the property
        yield [__DIR__ . '/../../../Fixtures/Fixtures23/STIWithPropertyColumn', 'STIWithPropertyColumn'];

        // Declaration via Column in the class
        yield [__DIR__ . '/../../../Fixtures/Fixtures23/STIWithClassColumn', 'STIWithClassColumn'];

        // Declaration via Table in the class
        yield [__DIR__ . '/../../../Fixtures/Fixtures23/STIWithTableColumn', 'STIWithTableColumn'];

        // Declaration via columns in the Entity
        yield [__DIR__ . '/../../../Fixtures/Fixtures23/STIWithEntityColumn', 'STIWithEntityColumn'];
    }

    /**
     * Must return 12 elements - 4 declarations x 3 readers.
     */
    public static function columnDeclarationWithReadersDataProvider(): \Traversable
    {
        $readers = \iterator_to_array(self::allReadersProvider(), false);
        $declarations = \iterator_to_array(self::columnDeclarationDataProvider());
        foreach ($readers as $reader) {
            foreach ($declarations as $declaration) {
                yield \array_merge($declaration, $reader);
            }
        }
    }

    #[DataProvider('allReadersProvider')]
    #[Test]
    public function testTableInheritance(ReaderInterface $reader): void
    {
        $tokenizer = new Tokenizer(
            new TokenizerConfig([
                'directories' => [__DIR__ . '/../../../Fixtures/Fixtures16'],
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

        Assert::notNull($person->getFooId());
        $loadedPerson = $this->orm->getRepository(Person::class)->findByPK($person->getFooId());
        Assert::instanceOf($loadedPerson, Person::class);
        Assert::same($loadedPerson->name, 'baz');
        Assert::false(isset($loadedPerson->preferences));
        Assert::false(isset($loadedPerson->salary));

        Assert::notNull($employee->getFooId());
        $loadedEmployee = $this->orm->getRepository(Person::class)->findByPK($employee->getFooId());
        Assert::instanceOf($loadedEmployee, Employee::class);
        Assert::same($loadedEmployee->name, 'foo');
        Assert::same($loadedEmployee->salary, 12500);
        Assert::same($loadedEmployee->bar, 'test');
        Assert::false(isset($loadedEmployee->preferences));

        Assert::notNull($customer->getFooId());
        $loadedCustomer = $this->orm->getRepository(Person::class)->findByPK($customer->getFooId());
        Assert::instanceOf($loadedCustomer, Customer::class);
        Assert::same($loadedCustomer->name, 'baz');
        Assert::same($loadedCustomer->preferences, 'private');
        Assert::false(isset($loadedCustomer->salary));

        Assert::notNull($ceo->getFooId());
        $loadedCeo = $this->orm->getRepository(Ceo::class)->findByPK($ceo->getFooId());
        Assert::instanceOf($loadedCeo, Ceo::class);
        Assert::same($loadedCeo->name, 'ceo');
        Assert::same($loadedCeo->salary, 150000);
        Assert::same($loadedCeo->stocks, 1000);
    }

    #[DataProvider('allReadersProvider')]
    #[Test]
    public function testTableInheritanceSchema(ReaderInterface $reader): void
    {
        $tokenizer = new Tokenizer(
            new TokenizerConfig([
                'directories' => [__DIR__ . '/../../../Fixtures/Fixtures20'],
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

        Assert::notBlank($schema);

        Assert::array($schema['person'])->hasKeys(SchemaInterface::CHILDREN);
        Assert::array($schema['person'])->hasKeys(SchemaInterface::DISCRIMINATOR);
        Assert::same($schema['person'][SchemaInterface::DISCRIMINATOR], 'type');

        Assert::array($schema['buyer'])->hasKeys(SchemaInterface::PARENT);
        Assert::array($schema['buyer'])->hasKeys(SchemaInterface::PARENT_KEY);
    }

    #[DataProvider('columnDeclarationWithReadersDataProvider')]
    #[Test]
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

        Assert::notBlank($schema);
        Assert::same($schema['baseEvent'][SchemaInterface::ENTITY], \sprintf(
            'Cycle\Annotated\Tests\Fixtures\Fixtures23\%s\BaseEvent',
            $namespace,
        ));
        Assert::same($schema['baseEvent'][SchemaInterface::MAPPER], Mapper::class);
        Assert::same($schema['baseEvent'][SchemaInterface::SOURCE], Source::class);
        Assert::same($schema['baseEvent'][SchemaInterface::REPOSITORY], Repository::class);
        Assert::same($schema['baseEvent'][SchemaInterface::DATABASE], 'default');
        Assert::same($schema['baseEvent'][SchemaInterface::TABLE], 'base_events');
        Assert::same($schema['baseEvent'][SchemaInterface::PRIMARY_KEY], ['id']);
        Assert::same($schema['baseEvent'][SchemaInterface::FIND_BY_KEYS], ['id']);
        Assert::count($schema['baseEvent'][SchemaInterface::COLUMNS], 4);
        Assert::same($schema['baseEvent'][SchemaInterface::COLUMNS]['id'], 'id');
        Assert::same($schema['baseEvent'][SchemaInterface::COLUMNS]['action'], 'action');
        Assert::same($schema['baseEvent'][SchemaInterface::COLUMNS]['object_id'], 'object_id');
        Assert::same($schema['baseEvent'][SchemaInterface::COLUMNS]['object_type'], 'object_type');
        Assert::same($schema['baseEvent'][SchemaInterface::RELATIONS]['object'][Relation::TYPE], Relation::BELONGS_TO_MORPHED);
        Assert::same($schema['baseEvent'][SchemaInterface::RELATIONS]['object'][Relation::TARGET], \sprintf('Cycle\Annotated\Tests\Fixtures\Fixtures23\%s\EventEmitterInterface', $namespace));
        Assert::same($schema['baseEvent'][SchemaInterface::RELATIONS]['object'][Relation::LOAD], Relation::LOAD_PROMISE);
        Assert::true($schema['baseEvent'][SchemaInterface::RELATIONS]['object'][Relation::SCHEMA][Relation::CASCADE]);
        Assert::true($schema['baseEvent'][SchemaInterface::RELATIONS]['object'][Relation::SCHEMA][Relation::NULLABLE]);
        Assert::same($schema['baseEvent'][SchemaInterface::RELATIONS]['object'][Relation::SCHEMA][Relation::OUTER_KEY], ['id']);
        Assert::same($schema['baseEvent'][SchemaInterface::RELATIONS]['object'][Relation::SCHEMA][Relation::INNER_KEY], 'object_id');
        Assert::same($schema['baseEvent'][SchemaInterface::RELATIONS]['object'][Relation::SCHEMA][Relation::MORPH_KEY], 'object_type');
        Assert::count($schema['baseEvent'][SchemaInterface::CHILDREN], 2);
        Assert::same($schema['baseEvent'][SchemaInterface::CHILDREN]['comment.created'], \sprintf('Cycle\Annotated\Tests\Fixtures\Fixtures23\%s\CommentCreated', $namespace));
        Assert::same($schema['baseEvent'][SchemaInterface::CHILDREN]['comment.updated'], \sprintf('Cycle\Annotated\Tests\Fixtures\Fixtures23\%s\CommentUpdated', $namespace));
        Assert::null($schema['baseEvent'][SchemaInterface::SCOPE]);
        Assert::same($schema['baseEvent'][SchemaInterface::TYPECAST]['id'], 'int');
        Assert::same($schema['baseEvent'][SchemaInterface::TYPECAST]['object_id'], 'int');
        Assert::same($schema['baseEvent'][SchemaInterface::SCHEMA], []);
        Assert::same($schema['baseEvent'][SchemaInterface::DISCRIMINATOR], 'action');
        Assert::null($schema['baseEvent'][SchemaInterface::TYPECAST_HANDLER]);
        Assert::same($schema['comment'], [
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
        Assert::same($schema['commentCreated'], [
            SchemaInterface::ENTITY => \sprintf(
                'Cycle\Annotated\Tests\Fixtures\Fixtures23\%s\CommentCreated',
                $namespace,
            ),
        ]);
        Assert::same($schema['commentUpdated'], [
            SchemaInterface::ENTITY => \sprintf(
                'Cycle\Annotated\Tests\Fixtures\Fixtures23\%s\CommentUpdated',
                $namespace,
            ),
        ]);
    }

    #[BeforeTest]
    public function createTables(): void
    {
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
