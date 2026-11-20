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
use Cycle\Annotated\Tests\Fixtures\Fixtures16\Executive;
use Cycle\Annotated\Tests\Functional\Driver\Common\BaseTestCase;
use Cycle\Annotated\Tests\Traits\TableTrait;
use Cycle\Database\Schema\AbstractForeignKey;
use Cycle\Database\Schema\AbstractIndex;
use Cycle\ORM\EntityManager;
use Cycle\ORM\Schema;
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
use Spiral\Attributes\ReaderInterface;
use Spiral\Tokenizer\Config\TokenizerConfig;
use Spiral\Tokenizer\Tokenizer;

abstract class JoinedTableTestCase extends BaseTestCase
{
    use TableTrait;

    #[\Testo\Data\DataProvider('allReadersProvider')]
    #[\Testo\Test]
    public function testTableInheritance(ReaderInterface $reader): void
    {
        $this->orm = $this->orm->with(new Schema($this->compile($reader)));

        $em = new EntityManager($this->orm);

        $executive = new Executive();
        $executive->bonus = 15000;
        $executive->name = 'executive';
        $executive->salary = 50000;
        $executive->hidden = 'secret';
        $executive->type = 'executive';
        $executive->proxyFieldWithAnnotation = 'value';

        $em->persist($executive);
        $em->run();

        $this->orm->getHeap()->clean();

        $loadedExecutive = $this->orm->getRepository(Executive::class)->findByPK($executive->getFooId());
        \Testo\Assert::instanceOf($loadedExecutive, Executive::class);
        \Testo\Assert::same($loadedExecutive->getName(), 'executive');
        \Testo\Assert::same($loadedExecutive->getSalary(), 50000);
        \Testo\Assert::same($loadedExecutive->hidden, 'secret');
        \Testo\Assert::same($loadedExecutive->bonus, 15000);
        \Testo\Assert::same($loadedExecutive->getType(), 'executive');
        \Testo\Assert::same($loadedExecutive->proxyFieldWithAnnotation, 'value');
    }

    #[\Testo\Data\DataProvider('allReadersProvider')]
    #[\Testo\Test]
    public function testAddForeignKeys(ReaderInterface $reader): void
    {
        $this->orm = $this->orm->with(new Schema($this->compile($reader)));
        $db = $this->dbal->database();

        $suppliersFk = $db->table('suppliers')->getForeignKeys();
        $executivesFk = $db->table('executives')->getForeignKeys();
        $externalSuppliersFk = $db->table('external_suppliers')->getForeignKeys();

        // simple case. Supplier -> Person
        \Testo\Assert::count($suppliersFk, 1);
        \Testo\Assert::instanceOf(reset($suppliersFk), AbstractForeignKey::class);
        \Testo\Assert::same(reset($suppliersFk)->getForeignTable(), 'people');
        \Testo\Assert::same(reset($suppliersFk)->getForeignKeys(), ['id']);
        \Testo\Assert::same(reset($suppliersFk)->getColumns(), ['id']);

        // Executive -> Employee (STI) -> Person
        \Testo\Assert::count($executivesFk, 1);
        \Testo\Assert::instanceOf(reset($executivesFk), AbstractForeignKey::class);
        \Testo\Assert::same(reset($executivesFk)->getForeignTable(), 'people');
        \Testo\Assert::same(reset($executivesFk)->getForeignKeys(), ['id']);
        \Testo\Assert::same(reset($executivesFk)->getColumns(), ['id']);

        // ExternalSupplier -> Supplier (JTI) -> Person. With outerKey
        \Testo\Assert::count($externalSuppliersFk, 1);
        \Testo\Assert::instanceOf(reset($externalSuppliersFk), AbstractForeignKey::class);
        \Testo\Assert::same(reset($externalSuppliersFk)->getForeignTable(), 'suppliers');
        \Testo\Assert::same(reset($externalSuppliersFk)->getForeignKeys(), ['custom_id']);
        \Testo\Assert::same(reset($externalSuppliersFk)->getColumns(), ['id']);
    }

    #[\Testo\Data\DataProvider('allReadersProvider')]
    #[\Testo\Test]
    public function testNotAddForeignKey(ReaderInterface $reader): void
    {
        $this->orm = $this->orm->with(new Schema($this->compile($reader)));

        \Testo\Assert::count($this->dbal->database()->table('buyers')->getForeignKeys(), 0);
    }

    #[\Testo\Data\DataProvider('allReadersProvider')]
    #[\Testo\Test]
    public function testAddIndex(ReaderInterface $reader): void
    {
        $this->orm = $this->orm->with(new Schema($this->compile($reader)));

        $indexes = $this->dbal->database()->table('suppliers')->getIndexes();

        // remove pk index
        $indexes = array_filter($indexes, fn(AbstractIndex $index) => $index->getColumns() !== ['id']);

        // one index added automatically, one added manual
        \Testo\Assert::count($indexes, 2);

        \Testo\Assert::true($this->dbal->database()->table('suppliers')->hasIndex(['index_id']));
        \Testo\Assert::true($this->dbal->database()->table('suppliers')->hasIndex(['custom_id']));

        \Testo\Assert::true(reset($indexes)->isUnique());
        \Testo\Assert::true(end($indexes)->isUnique());
    }

    #[\Testo\Data\DataProvider('allReadersProvider')]
    #[\Testo\Test]
    public function testJtiParentColumns(ReaderInterface $reader): void
    {
        $schema = $this->compile($reader, 'Fixtures21');

        \Testo\Assert::notBlank($schema);

        \Testo\Assert::array($schema['person'])->hasKeys(SchemaInterface::COLUMNS);

        // assert that parent doesn't have jti columns
        \Testo\Assert::equals($schema['person'][SchemaInterface::COLUMNS], [
            'id' => 'id',
            'name' => 'name',
            'type' => 'type',
        ]);
    }

    #[\Testo\Lifecycle\BeforeTest]
    public function setUp(): void
    {
        parent::setUp();

        $this->makeTable(
            table: 'people',
            columns: [
                'id' => 'primary',
                'type' => 'string',
                'name' => 'string',
                'salary' => 'float',
                'preferences' => 'string',
                'bar' => 'string',
                'stocks' => 'int',
            ],
        );

        $this->makeTable(
            table: 'executives',
            columns: [
                'id' => 'int',
                'bonus' => 'float',
                'proxy' => 'string',
                'hidden' => 'string',
            ],
            pk: ['id'],
        );

        $this->makeTable(
            table: 'suppliers',
            columns: [
                'id' => 'int',
                'custom_id' => 'int',
            ],
            pk: ['id'],
        );

        $this->makeTable(
            table: 'external_suppliers',
            columns: [
                'id' => 'int',
            ],
            pk: ['id'],
        );

        $this->makeTable(
            table: 'local_suppliers',
            columns: [
                'id' => 'int',
            ],
            pk: ['id'],
        );

        $this->makeTable(
            table: 'buyers',
            columns: [
                'id' => 'int',
            ],
            pk: ['id'],
        );
    }

    private function compile(ReaderInterface $reader, string $fixtures = 'Fixtures16'): array
    {
        $tokenizer = new Tokenizer(
            new TokenizerConfig([
                'directories' => [sprintf(__DIR__ . '/../../../../Fixtures/%s', $fixtures)],
                'exclude' => [],
            ]),
        );

        $locator = $tokenizer->classLocator();

        return (new Compiler())->compile(new Registry($this->dbal), [
            new ResetTables(),
            new Embeddings(new TokenizerEmbeddingLocator($locator, $reader), $reader),
            new Entities(new TokenizerEntityLocator($locator, $reader), $reader),
            new TableInheritance($reader),
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
