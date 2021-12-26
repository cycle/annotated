<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Functional\Driver\Common\Inheritance;

use Cycle\Annotated\Embeddings;
use Cycle\Annotated\Entities;
use Cycle\Annotated\MergeColumns;
use Cycle\Annotated\MergeIndexes;
use Cycle\Annotated\TableInheritance;
use Cycle\Annotated\Tests\Fixtures\Fixtures16\Executive;
use Cycle\Annotated\Tests\Functional\Driver\Common\BaseTest;
use Cycle\Annotated\Tests\Traits\TableTrait;
use Cycle\Database\Schema\AbstractForeignKey;
use Cycle\ORM\EntityManager;
use Cycle\ORM\Schema;
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

abstract class JoinedTableTest extends BaseTest
{
    use TableTrait;

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
            ]
        );

        $this->makeTable(
            table: 'executives',
            columns: [
                'id' => 'int',
                'bonus' => 'float',
                'proxy' => 'string',
                'hidden' => 'string',
            ],
            pk: ['id']
        );

        $this->makeTable(
            table: 'suppliers',
            columns: [
                'id' => 'int',
            ],
            pk: ['id']
        );

        $this->makeTable(
            table: 'external_suppliers',
            columns: [
                'id' => 'int',
            ],
            pk: ['id']
        );

        $this->makeTable(
            table: 'buyers',
            columns: [
                'id' => 'int',
            ],
            pk: ['id']
        );
    }

    /**
     * @dataProvider allReadersProvider
     */
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
        $this->assertInstanceOf(Executive::class, $loadedExecutive);
        $this->assertSame('executive', $loadedExecutive->getName());
        $this->assertSame(50000, $loadedExecutive->getSalary());
        $this->assertSame('secret', $loadedExecutive->hidden);
        $this->assertSame(15000, $loadedExecutive->bonus);
        $this->assertSame('executive', $loadedExecutive->getType());
        $this->assertNull($loadedExecutive->proxyFieldWithAnnotation);
    }

    /**
     * @dataProvider allReadersProvider
     */
    public function testAddForeignKeys(ReaderInterface $reader): void
    {
        $this->orm = $this->orm->with(new Schema($this->compile($reader)));
        $db = $this->dbal->database();

        $suppliersFk = $db->table('suppliers')->getForeignKeys();
        $executivesFk = $db->table('executives')->getForeignKeys();
        $externalSuppliersFk = $db->table('external_suppliers')->getForeignKeys();

        // simple case. Supplier -> Person
        $this->assertIsArray($suppliersFk);
        $this->assertCount(1, $suppliersFk);
        $this->assertInstanceOf(AbstractForeignKey::class, $suppliersFk['suppliers_id_fk']);
        $this->assertSame('people', $suppliersFk['suppliers_id_fk']->getForeignTable());

        // Executive -> Employee (STI) -> Person
        $this->assertIsArray($executivesFk);
        $this->assertCount(1, $executivesFk);
        $this->assertInstanceOf(AbstractForeignKey::class, $executivesFk['executives_id_fk']);
        $this->assertSame('people', $executivesFk['executives_id_fk']->getForeignTable());

        // ExternalSupplier -> Supplier (JTI) -> Person
        $this->assertIsArray($externalSuppliersFk);
        $this->assertCount(1, $externalSuppliersFk);
        $this->assertInstanceOf(AbstractForeignKey::class, $externalSuppliersFk['external_suppliers_id_fk']);
        $this->assertSame('suppliers', $externalSuppliersFk['external_suppliers_id_fk']->getForeignTable());
    }

    /**
     * @dataProvider allReadersProvider
     */
    public function testNotAddForeignKey(ReaderInterface $reader): void
    {
        $this->orm = $this->orm->with(new Schema($this->compile($reader)));

        $this->assertCount(0, $this->dbal->database()->table('buyers')->getForeignKeys());
    }

    private function compile(ReaderInterface $reader): array
    {
        $tokenizer = new Tokenizer(
            new TokenizerConfig([
                'directories' => [__DIR__ . '/../../../../Fixtures/Fixtures16'],
                'exclude' => [],
            ])
        );

        $locator = $tokenizer->classLocator();

        return (new Compiler())->compile(new Registry($this->dbal), [
            new ResetTables(),
            new Embeddings($locator, $reader),
            new Entities($locator, $reader),
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
