<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Feature\Common;

use Cycle\Annotated\Entities;
use Cycle\Annotated\Locator\TokenizerEntityLocator;
use Cycle\Annotated\MergeColumns;
use Cycle\Annotated\MergeIndexes;
use Cycle\Annotated\Tests\Feature\BaseTestCase;
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
use Spiral\Attributes\AnnotationReader;
use Spiral\Attributes\AttributeReader;
use Spiral\Attributes\Composite\MergeReader;
use Spiral\Attributes\ReaderInterface;
use Spiral\Tokenizer\Config\TokenizerConfig;
use Spiral\Tokenizer\Tokenizer;
use Testo\Assert;
use Testo\Data\DataProvider;
use Testo\Test;

final class GeneratorTest extends BaseTestCase
{
    #[Test]
    public function testCreateEntitiesWithNullReader(): void
    {
        $r = new Registry($this->dbal);
        (new Entities(new TokenizerEntityLocator($this->locator)))->run($r);

        Assert::true($r->hasEntity(Simple::class));
        Assert::true($r->hasEntity(WithTable::class));
        Assert::true($r->hasEntity(Complete::class));
    }

    #[Test]
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
        Assert::true($r->hasEntity(Simple::class));
        Assert::true($r->hasEntity(WithTable::class));
        Assert::true($r->hasEntity(Complete::class));
        // MergeColumns
        $schema = $r->getTableSchema($r->getEntity('withTable'));
        Assert::true($schema->hasColumn('name'));
        Assert::same($schema->column('status')->getType(), 'string');
        // MergeIndexes
        Assert::true($schema->hasIndex(['name']));
        Assert::true($schema->index(['name'])->isUnique());
        Assert::same($schema->index(['name'])->getName(), 'name_index');
    }

    #[Test]
    public function testCreateGeneratorsWithCustomMergeReader(): void
    {
        $reader = new MergeReader([new AnnotationReader(), new AttributeReader()]);
        $r = new Registry($this->dbal);

        $tokenizer = new Tokenizer(new TokenizerConfig([
            'directories' => [__DIR__ . '/../../Fixtures/Fixtures10'],
            'exclude' => [],
        ]));
        $locator = $tokenizer->classLocator();

        (new Entities(new TokenizerEntityLocator($locator, $reader), $reader))->run($r);
        (new MergeColumns($reader))->run($r);
        (new RenderTables())->run($r);
        (new MergeIndexes($reader))->run($r);
        (new SyncTables())->run($r);


        Assert::true($r->hasTable($r->getEntity('MergedMeta')));
        $schema = $r->getTableSchema($r->getEntity('MergedMeta'));
        Assert::true($schema->hasColumn('name'));
        Assert::true($schema->hasIndex(['name', 'id DESC']));
    }

    #[DataProvider('allReadersProvider')]
    #[Test]
    public function testLocateAll(ReaderInterface $reader): void
    {
        $r = new Registry($this->dbal);
        (new Entities(new TokenizerEntityLocator($this->locator, $reader), $reader))->run($r);

        Assert::true($r->hasEntity(Simple::class));
        Assert::true($r->hasEntity(WithTable::class));
        Assert::true($r->hasEntity(Complete::class));
    }

    #[DataProvider('allReadersProvider')]
    #[Test]
    public function testSimpleSchema(ReaderInterface $reader): void
    {
        $r = new Registry($this->dbal);
        (new Entities(new TokenizerEntityLocator($this->locator, $reader), $reader))->run($r);

        Assert::true($r->hasEntity(Simple::class));
        Assert::true($r->hasEntity('simple'));

        Assert::same($r->getEntity('simple')->getMapper(), null);
        Assert::same($r->getEntity('simple')->getRepository(), null);

        Assert::true($r->hasTable($r->getEntity('simple')));
        Assert::same($r->getDatabase($r->getEntity('simple')), 'default');
        Assert::same($r->getTable($r->getEntity('simple')), 'simples');

        Assert::true($r->getEntity('simple')->getFields()->has('id'));
        Assert::same($r->getEntity('simple')->getFields()->get('id')->getColumn(), 'id');
    }

    #[DataProvider('allReadersProvider')]
    #[Test]
    public function testCompleteSchema(ReaderInterface $reader): void
    {
        $r = new Registry($this->dbal);
        (new Entities(new TokenizerEntityLocator($this->locator, $reader), $reader))->run($r);

        Assert::true($r->hasEntity(Complete::class));
        Assert::true($r->hasEntity('eComplete'));

        Assert::same($r->getEntity('eComplete')->getMapper(), CompleteMapper::class);
        Assert::same($r->getEntity('eComplete')->getRepository(), CompleteRepository::class);
        Assert::same($r->getEntity('eComplete')->getSource(), TestSource::class);
        Assert::same($r->getEntity('eComplete')->getScope(), SomeConstrain::class);

        Assert::true($r->hasTable($r->getEntity('eComplete')));
        Assert::same($r->getDatabase($r->getEntity('eComplete')), 'secondary');
        Assert::same($r->getTable($r->getEntity('eComplete')), 'complete_data');

        Assert::true($r->getEntity('eComplete')->getFields()->has('id'));
        Assert::true($r->getEntity('eComplete')->getFields()->has('name'));

        Assert::same($r->getEntity('eComplete')->getFields()->get('name')->getColumn(), 'username');

        Assert::false($r->getEntity('eComplete')->getFields()->has('ignored'));
    }
}
