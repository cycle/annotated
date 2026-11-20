<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Functional\Driver\Common;

use Cycle\Annotated\Entities;
use Cycle\Annotated\Locator\TokenizerEntityLocator;
use Cycle\Annotated\MergeColumns;
use Cycle\Annotated\MergeIndexes;
use Cycle\Annotated\Tests\Fixtures\Fixtures1\Complete;
use Cycle\Annotated\Tests\Fixtures\Fixtures1\CompleteMapper;
use Cycle\Annotated\Tests\Fixtures\Fixtures1\Constrain\SomeConstrain;
use Cycle\Annotated\Tests\Fixtures\Fixtures1\Repository\CompleteRepository;
use Cycle\Annotated\Tests\Fixtures\Fixtures1\Simple;
use Cycle\Annotated\Tests\Fixtures\Fixtures1\Source\TestSource;
use Cycle\Annotated\Tests\Fixtures\Fixtures1\WithTable;
use Cycle\Schema\Generator\RenderTables;
use Cycle\Schema\Generator\SyncTables;
use Cycle\Schema\Registry;
use Doctrine\Common\Annotations\AnnotationReader as DoctrineAnnotationReader;
use PHPUnit\Framework\Attributes\DataProvider;
use Spiral\Attributes\AnnotationReader;
use Spiral\Attributes\AttributeReader;
use Spiral\Attributes\Composite\MergeReader;
use Spiral\Attributes\ReaderInterface;
use Spiral\Tokenizer\Config\TokenizerConfig;
use Spiral\Tokenizer\Tokenizer;

abstract class GeneratorTestCase extends BaseTestCase
{
    #[\Testo\Test]
    public function testCreateEntitiesWithNullReader(): void
    {
        $r = new Registry($this->dbal);
        (new Entities(new TokenizerEntityLocator($this->locator)))->run($r);

        \Testo\Assert::true($r->hasEntity(Simple::class));
        \Testo\Assert::true($r->hasEntity(WithTable::class));
        \Testo\Assert::true($r->hasEntity(Complete::class));
    }

    #[\Testo\Test]
    public function testCreateGeneratorsWithDoctrineAnnotationReader(): void
    {
        $reader = new DoctrineAnnotationReader();
        $r = new Registry($this->dbal);

        (new Entities(new TokenizerEntityLocator($this->locator, $reader), $reader))->run($r);
        (new MergeColumns($reader))->run($r);
        (new RenderTables())->run($r);
        (new MergeIndexes($reader))->run($r);
        (new SyncTables())->run($r);

        // Entities
        \Testo\Assert::true($r->hasEntity(Simple::class));
        \Testo\Assert::true($r->hasEntity(WithTable::class));
        \Testo\Assert::true($r->hasEntity(Complete::class));
        // MergeColumns
        $schema = $r->getTableSchema($r->getEntity('withTable'));
        \Testo\Assert::true($schema->hasColumn('name'));
        \Testo\Assert::same($schema->column('status')->getType(), 'string');
        // MergeIndexes
        \Testo\Assert::true($schema->hasIndex(['name']));
        \Testo\Assert::true($schema->index(['name'])->isUnique());
        \Testo\Assert::same($schema->index(['name'])->getName(), 'name_index');
    }

    #[\Testo\Test]
    public function testCreateGeneratorsWithCustomMergeReader(): void
    {
        $reader = new MergeReader([new AnnotationReader(), new AttributeReader()]);
        $r = new Registry($this->dbal);

        $tokenizer = new Tokenizer(new TokenizerConfig([
            'directories' => [__DIR__ . '/../../../Fixtures/Fixtures10'],
            'exclude' => [],
        ]));
        $locator = $tokenizer->classLocator();

        (new Entities(new TokenizerEntityLocator($locator, $reader), $reader))->run($r);
        (new MergeColumns($reader))->run($r);
        (new RenderTables())->run($r);
        (new MergeIndexes($reader))->run($r);
        (new SyncTables())->run($r);


        \Testo\Assert::true($r->hasTable($r->getEntity('MergedMeta')));
        $schema = $r->getTableSchema($r->getEntity('MergedMeta'));
        \Testo\Assert::true($schema->hasColumn('name'));
        \Testo\Assert::true($schema->hasIndex(['name', 'id DESC']));
    }

    #[\Testo\Data\DataProvider('allReadersProvider')]
    #[\Testo\Test]
    public function testLocateAll(ReaderInterface $reader): void
    {
        $r = new Registry($this->dbal);
        (new Entities(new TokenizerEntityLocator($this->locator, $reader), $reader))->run($r);

        \Testo\Assert::true($r->hasEntity(Simple::class));
        \Testo\Assert::true($r->hasEntity(WithTable::class));
        \Testo\Assert::true($r->hasEntity(Complete::class));
    }

    #[\Testo\Data\DataProvider('allReadersProvider')]
    #[\Testo\Test]
    public function testSimpleSchema(ReaderInterface $reader): void
    {
        $r = new Registry($this->dbal);
        (new Entities(new TokenizerEntityLocator($this->locator, $reader), $reader))->run($r);

        \Testo\Assert::true($r->hasEntity(Simple::class));
        \Testo\Assert::true($r->hasEntity('simple'));

        \Testo\Assert::same($r->getEntity('simple')->getMapper(), null);
        \Testo\Assert::same($r->getEntity('simple')->getRepository(), null);

        \Testo\Assert::true($r->hasTable($r->getEntity('simple')));
        \Testo\Assert::same($r->getDatabase($r->getEntity('simple')), 'default');
        \Testo\Assert::same($r->getTable($r->getEntity('simple')), 'simples');

        \Testo\Assert::true($r->getEntity('simple')->getFields()->has('id'));
        \Testo\Assert::same($r->getEntity('simple')->getFields()->get('id')->getColumn(), 'id');
    }

    #[\Testo\Data\DataProvider('allReadersProvider')]
    #[\Testo\Test]
    public function testCompleteSchema(ReaderInterface $reader): void
    {
        $r = new Registry($this->dbal);
        (new Entities(new TokenizerEntityLocator($this->locator, $reader), $reader))->run($r);

        \Testo\Assert::true($r->hasEntity(Complete::class));
        \Testo\Assert::true($r->hasEntity('eComplete'));

        \Testo\Assert::same($r->getEntity('eComplete')->getMapper(), CompleteMapper::class);
        \Testo\Assert::same($r->getEntity('eComplete')->getRepository(), CompleteRepository::class);
        \Testo\Assert::same($r->getEntity('eComplete')->getSource(), TestSource::class);
        \Testo\Assert::same($r->getEntity('eComplete')->getScope(), SomeConstrain::class);

        \Testo\Assert::true($r->hasTable($r->getEntity('eComplete')));
        \Testo\Assert::same($r->getDatabase($r->getEntity('eComplete')), 'secondary');
        \Testo\Assert::same($r->getTable($r->getEntity('eComplete')), 'complete_data');

        \Testo\Assert::true($r->getEntity('eComplete')->getFields()->has('id'));
        \Testo\Assert::true($r->getEntity('eComplete')->getFields()->has('name'));

        \Testo\Assert::same($r->getEntity('eComplete')->getFields()->get('name')->getColumn(), 'username');

        \Testo\Assert::false($r->getEntity('eComplete')->getFields()->has('ignored'));
    }
}
