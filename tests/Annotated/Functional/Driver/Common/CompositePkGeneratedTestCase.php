<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Functional\Driver\Common;

use Cycle\Annotated\Entities;
use Cycle\Annotated\Locator\TokenizerEntityLocator;
use Cycle\Annotated\MergeColumns;
use Cycle\Annotated\MergeIndexes;
use Cycle\Annotated\Tests\Fixtures\Fixtures28\Event;
use Cycle\ORM\Config\RelationConfig;
use Cycle\ORM\EntityManager;
use Cycle\ORM\Factory;
use Cycle\ORM\ORM;
use Cycle\ORM\Schema;
use Cycle\ORM\Schema\GeneratedField;
use Cycle\ORM\SchemaInterface;
use Cycle\ORM\Select;
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

/**
 * Composite primary key where one part is application-supplied (uuid) and the
 * other is database-generated (bigserial), populated through a RETURNING clause.
 *
 * Mirrors cycle/orm "Case8" but driven entirely by attributes/annotations.
 * The round-trip relies on a driver implementing ReturningInterface
 * (PostgreSQL, SQL Server), so it lives in those driver suites.
 */
abstract class CompositePkGeneratedTestCase extends BaseTestCase
{
    /**
     * The composite PK and the database-generated component must be derived
     * from the attributes alone.
     */
    #[DataProvider('allReadersProvider')]
    public function testCompositePkSchema(ReaderInterface $reader): void
    {
        $schema = $this->compile($reader);

        $this->assertSame(['parentId', 'id'], $schema['event'][SchemaInterface::PRIMARY_KEY]);
        $this->assertSame(
            ['parentId' => 'parent_id', 'id' => 'id', 'payload' => 'payload'],
            $schema['event'][SchemaInterface::COLUMNS],
        );

        // Both primary columns are flagged ON_INSERT: the bigserial `id` is
        // genuinely database-generated, while the app-supplied uuid `parentId`
        // is auto-flagged purely because it is primary (see Event::$parentId
        // and Configurator::isOnInsertGeneratedField()). The parentId flag only
        // echoes the value back via RETURNING — harmless for a uuid string.
        $this->assertSame(
            [
                'parentId' => GeneratedField::ON_INSERT,
                'id' => GeneratedField::ON_INSERT,
            ],
            $schema['event'][SchemaInterface::GENERATED_FIELDS],
        );
    }

    /**
     * Newly created siblings under the same parent receive distinct,
     * database-generated ids returned on INSERT, and can be fetched back by
     * the full composite key.
     */
    #[DataProvider('allReadersProvider')]
    public function testCreateGeneratesIdAndFetchByCompositePk(ReaderInterface $reader): void
    {
        $orm = new ORM(
            new Factory($this->dbal, RelationConfig::getDefault()),
            new Schema($this->compile($reader)),
        );

        $parentId = '0190a000-0000-7000-8000-000000000001';

        $first = new Event();
        $first->parentId = $parentId;
        $first->payload = 'first';

        $second = new Event();
        $second->parentId = $parentId;
        $second->payload = 'second';

        (new EntityManager($orm))->persist($first)->persist($second)->run();

        // database-generated ids
        $this->assertIsInt($first->id);
        $this->assertIsInt($second->id);
        $this->assertNotSame($first->id, $second->id);

        // fetch by the full composite key
        $found = (new Select($orm, Event::class))
            ->where('parentId', $parentId)
            ->where('id', $second->id)
            ->fetchOne();

        $this->assertNotNull($found);
        $this->assertSame('second', $found->payload);
        $this->assertSame($second, $found, 'Heap returns the same identity for the composite key.');
    }

    private function compile(ReaderInterface $reader): array
    {
        $tokenizer = new Tokenizer(new TokenizerConfig([
            'directories' => [__DIR__ . '/../../../Fixtures/Fixtures28'],
            'exclude' => [],
        ]));

        $locator = $tokenizer->classLocator();

        return (new Compiler())->compile(new Registry($this->dbal), [
            new Entities(new TokenizerEntityLocator($locator, $reader), $reader),
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
