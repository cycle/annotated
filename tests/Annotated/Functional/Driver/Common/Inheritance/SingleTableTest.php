<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Functional\Driver\Common\Inheritance;

use Cycle\Annotated\Embeddings;
use Cycle\Annotated\Entities;
use Cycle\Annotated\MergeColumns;
use Cycle\Annotated\MergeIndexes;
use Cycle\Annotated\TableInheritance;
use Cycle\Annotated\Tests\Fixtures\Fixtures16\Customer;
use Cycle\Annotated\Tests\Fixtures\Fixtures16\Employee;
use Cycle\Annotated\Tests\Fixtures\Fixtures16\Person;
use Cycle\Annotated\Tests\Fixtures\Fixtures16\Ceo;
use Cycle\Annotated\Tests\Functional\Driver\Common\BaseTest;
use Cycle\Annotated\Tests\Traits\TableTrait;
use Cycle\ORM\Mapper\Mapper;
use Cycle\ORM\Relation;
use Cycle\ORM\Schema;
use Cycle\ORM\SchemaInterface;
use Cycle\ORM\Select\Repository;
use Cycle\ORM\Select\Source;
use Cycle\ORM\Transaction;
use Cycle\Schema\Compiler;
use Cycle\Schema\Generator\GenerateRelations;
use Cycle\Schema\Generator\GenerateTypecast;
use Cycle\Schema\Generator\RenderRelations;
use Cycle\Schema\Generator\RenderTables;
use Cycle\Schema\Generator\ResetTables;
use Cycle\Schema\Generator\SyncTables;
use Cycle\Schema\Registry;
use Spiral\Attributes\AnnotationReader;
use Spiral\Attributes\AttributeReader;
use Spiral\Attributes\Composite\SelectiveReader;
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

        $this->orm = $this->orm->with(new Schema($schema));

        $t = new Transaction($this->orm);

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
            new Embeddings($locator, $reader),
            new Entities($locator, $reader),
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

    /**
     * @dataProvider columnDeclarationDataProvider
     */
    public function testSingleTableInheritanceWithDifferentColumnDeclaration(
        string $directory,
        ReaderInterface $reader,
        string $namespace
    ): void {
        $tokenizer = new Tokenizer(
            new TokenizerConfig([
                'directories' => [$directory],
                'exclude' => [],
            ])
        );

        $locator = $tokenizer->classLocator();

        $r = new Registry($this->dbal);

        $schema = (new Compiler())->compile($r, [
            new Embeddings($locator, $reader),
            new Entities($locator, $reader),
            new MergeColumns($reader),
            new TableInheritance($reader),
            new ResetTables(),
            new GenerateRelations(),
            new RenderTables(),
            new RenderRelations(),
            new MergeIndexes($reader),
            new GenerateTypecast(),
        ]);

        $this->assertNotEmpty($schema);
        $this->assertSame(\sprintf(
            'Cycle\Annotated\Tests\Fixtures\Fixtures23\%s\BaseEvent',
            $namespace
        ), $schema['baseEvent'][SchemaInterface::ENTITY]);
        $this->assertSame(Mapper::class, $schema['baseEvent'][SchemaInterface::MAPPER]);
        $this->assertSame(Source::class, $schema['baseEvent'][SchemaInterface::SOURCE]);
        $this->assertSame(Repository::class, $schema['baseEvent'][SchemaInterface::REPOSITORY]);
        $this->assertSame('default', $schema['baseEvent'][SchemaInterface::DATABASE]);
        $this->assertSame('base_events', $schema['baseEvent'][SchemaInterface::TABLE]);
        $this->assertSame(['id'], $schema['baseEvent'][SchemaInterface::PRIMARY_KEY]);
        $this->assertSame(['id'], $schema['baseEvent'][SchemaInterface::FIND_BY_KEYS]);
        $this->assertCount(4, $schema['baseEvent'][SchemaInterface::COLUMNS]);
        $this->assertSame('id', $schema['baseEvent'][SchemaInterface::COLUMNS]['id']);
        $this->assertSame('action', $schema['baseEvent'][SchemaInterface::COLUMNS]['action']);
        $this->assertSame('object_id', $schema['baseEvent'][SchemaInterface::COLUMNS]['object_id']);
        $this->assertSame('object_type', $schema['baseEvent'][SchemaInterface::COLUMNS]['object_type']);
        $this->assertSame(
            Relation::BELONGS_TO_MORPHED,
            $schema['baseEvent'][SchemaInterface::RELATIONS]['object'][Relation::TYPE]
        );
        $this->assertSame(
            \sprintf('Cycle\Annotated\Tests\Fixtures\Fixtures23\%s\EventEmitterInterface', $namespace),
            $schema['baseEvent'][SchemaInterface::RELATIONS]['object'][Relation::TARGET]
        );
        $this->assertSame(
            Relation::LOAD_PROMISE,
            $schema['baseEvent'][SchemaInterface::RELATIONS]['object'][Relation::LOAD]
        );
        $this->assertTrue(
            $schema['baseEvent'][SchemaInterface::RELATIONS]['object'][Relation::SCHEMA][Relation::CASCADE]
        );
        $this->assertTrue(
            $schema['baseEvent'][SchemaInterface::RELATIONS]['object'][Relation::SCHEMA][Relation::NULLABLE]
        );
        $this->assertSame(
            ['id'],
            $schema['baseEvent'][SchemaInterface::RELATIONS]['object'][Relation::SCHEMA][Relation::OUTER_KEY]
        );
        $this->assertSame(
            'object_id',
            $schema['baseEvent'][SchemaInterface::RELATIONS]['object'][Relation::SCHEMA][Relation::INNER_KEY]
        );
        $this->assertSame(
            'object_type',
            $schema['baseEvent'][SchemaInterface::RELATIONS]['object'][Relation::SCHEMA][Relation::MORPH_KEY]
        );
        $this->assertCount(2, $schema['baseEvent'][SchemaInterface::CHILDREN]);
        $this->assertSame(
            \sprintf('Cycle\Annotated\Tests\Fixtures\Fixtures23\%s\CommentCreated', $namespace),
            $schema['baseEvent'][SchemaInterface::CHILDREN]['comment.created']
        );
        $this->assertSame(
            \sprintf('Cycle\Annotated\Tests\Fixtures\Fixtures23\%s\CommentUpdated', $namespace),
            $schema['baseEvent'][SchemaInterface::CHILDREN]['comment.updated']
        );
        $this->assertNull($schema['baseEvent'][SchemaInterface::SCOPE]);
        $this->assertSame('int', $schema['baseEvent'][SchemaInterface::TYPECAST]['id']);
        $this->assertSame('int', $schema['baseEvent'][SchemaInterface::TYPECAST]['object_id']);
        $this->assertSame([], $schema['baseEvent'][SchemaInterface::SCHEMA]);
        $this->assertSame('action', $schema['baseEvent'][SchemaInterface::DISCRIMINATOR]);
        $this->assertNull($schema['baseEvent'][SchemaInterface::TYPECAST_HANDLER]);
        $this->assertSame(
            [
                SchemaInterface::ENTITY => \sprintf(
                    'Cycle\Annotated\Tests\Fixtures\Fixtures23\%s\Comment',
                    $namespace
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
                ],
                SchemaInterface::SCHEMA => [],
                SchemaInterface::TYPECAST_HANDLER => null,
            ],
            $schema['comment']
        );
        $this->assertSame([
            SchemaInterface::ENTITY => \sprintf(
                'Cycle\Annotated\Tests\Fixtures\Fixtures23\%s\CommentCreated',
                $namespace
            ),
        ], $schema['commentCreated']);
        $this->assertSame([
            SchemaInterface::ENTITY => \sprintf(
                'Cycle\Annotated\Tests\Fixtures\Fixtures23\%s\CommentUpdated',
                $namespace
            ),
        ], $schema['commentUpdated']);
    }

    public function columnDeclarationDataProvider(): \Traversable
    {
        // Declaration via Column in the property
        yield [
            __DIR__ . '/../../../../Fixtures/Fixtures23/STIWithPropertyColumn',
            new AttributeReader(),
            'STIWithPropertyColumn',
        ];
        yield [
            __DIR__ . '/../../../../Fixtures/Fixtures23/STIWithPropertyColumn',
            new AnnotationReader(),
            'STIWithPropertyColumn',
        ];
        yield [
            __DIR__ . '/../../../../Fixtures/Fixtures23/STIWithPropertyColumn',
            new SelectiveReader([new AttributeReader(), new AnnotationReader()]),
            'STIWithPropertyColumn',
        ];

        // Declaration via Column in the class
        yield [
            __DIR__ . '/../../../../Fixtures/Fixtures23/STIWithClassColumn',
            new AttributeReader(),
            'STIWithClassColumn',
        ];
        yield [
            __DIR__ . '/../../../../Fixtures/Fixtures23/STIWithClassColumn',
            new AnnotationReader(),
            'STIWithClassColumn',
        ];
        yield [
            __DIR__ . '/../../../../Fixtures/Fixtures23/STIWithClassColumn',
            new SelectiveReader([new AttributeReader(), new AnnotationReader()]),
            'STIWithClassColumn',
        ];

        // Declaration via Table in the class
        yield [
            __DIR__ . '/../../../../Fixtures/Fixtures23/STIWithTableColumn',
            new AnnotationReader(),
            'STIWithTableColumn',
        ];

        // Declaration via columns in the Entity
        yield [
            __DIR__ . '/../../../../Fixtures/Fixtures23/STIWithEntityColumn',
            new AnnotationReader(),
            'STIWithEntityColumn',
        ];
    }
}
