<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Functional\Driver\Common;

use Cycle\Annotated\Entities;
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
use Spiral\Attributes\AnnotationReader;
use Spiral\Attributes\AttributeReader;
use Spiral\Attributes\Composite\MergeReader;
use Spiral\Attributes\ReaderInterface;
use Spiral\Tokenizer\Config\TokenizerConfig;
use Spiral\Tokenizer\Tokenizer;

abstract class GeneratorTest extends BaseTest
{
    public function testCreateEntitiesWithNullReader(): void
    {
        $r = new Registry($this->dbal);
        (new Entities($this->locator))->run($r);

        $this->assertTrue($r->hasEntity(Simple::class));
        $this->assertTrue($r->hasEntity(WithTable::class));
        $this->assertTrue($r->hasEntity(Complete::class));
    }

    public function testCreateGeneratorsWithDoctrineAnnotationReader(): void
    {
        $reader = new DoctrineAnnotationReader();
        $r = new Registry($this->dbal);

        (new Entities($this->locator, $reader))->run($r);
        (new MergeColumns($reader))->run($r);
        (new RenderTables())->run($r);
        (new MergeIndexes($reader))->run($r);
        (new SyncTables())->run($r);

        // Entities
        $this->assertTrue($r->hasEntity(Simple::class));
        $this->assertTrue($r->hasEntity(WithTable::class));
        $this->assertTrue($r->hasEntity(Complete::class));
        // MergeColumns
        $schema = $r->getTableSchema($r->getEntity('withTable'));
        $this->assertTrue($schema->hasColumn('name'));
        $this->assertSame('string', $schema->column('status')->getType());
        // MergeIndexes
        $this->assertTrue($schema->hasIndex(['name']));
        $this->assertTrue($schema->index(['name'])->isUnique());
        $this->assertSame('name_index', $schema->index(['name'])->getName());
    }

    public function testCreateGeneratorsWithCustomMergeReader(): void
    {
        $reader = new MergeReader([new AnnotationReader(), new AttributeReader()]);
        $r = new Registry($this->dbal);

        $tokenizer = new Tokenizer(new TokenizerConfig([
            'directories' => [__DIR__ . '/../../../Fixtures/Fixtures10'],
            'exclude' => [],
        ]));
        $locator = $tokenizer->classLocator();

        (new Entities($locator, $reader))->run($r);
        (new MergeColumns($reader))->run($r);
        (new RenderTables())->run($r);
        (new MergeIndexes($reader))->run($r);
        (new SyncTables())->run($r);


        $this->assertTrue($r->hasTable($r->getEntity('MergedMeta')));
        $schema = $r->getTableSchema($r->getEntity('MergedMeta'));
        $this->assertTrue($schema->hasColumn('name'));
        $this->assertTrue($schema->hasIndex(['name', 'id DESC']));
    }

    /**
     * @dataProvider allReadersProvider
     */
    public function testLocateAll(ReaderInterface $reader): void
    {
        $r = new Registry($this->dbal);
        (new Entities($this->locator, $reader))->run($r);

        $this->assertTrue($r->hasEntity(Simple::class));
        $this->assertTrue($r->hasEntity(WithTable::class));
        $this->assertTrue($r->hasEntity(Complete::class));
    }

    /**
     * @dataProvider allReadersProvider
     */
    public function testSimpleSchema(ReaderInterface $reader): void
    {
        $r = new Registry($this->dbal);
        (new Entities($this->locator, $reader))->run($r);

        $this->assertTrue($r->hasEntity(Simple::class));
        $this->assertTrue($r->hasEntity('simple'));

        $this->assertSame(null, $r->getEntity('simple')->getMapper());
        $this->assertSame(null, $r->getEntity('simple')->getRepository());

        $this->assertTrue($r->hasTable($r->getEntity('simple')));
        $this->assertSame('default', $r->getDatabase($r->getEntity('simple')));
        $this->assertSame('simples', $r->getTable($r->getEntity('simple')));

        $this->assertTrue($r->getEntity('simple')->getFields()->has('id'));
        $this->assertSame('id', $r->getEntity('simple')->getFields()->get('id')->getColumn());
    }

    /**
     * @dataProvider allReadersProvider
     */
    public function testCompleteSchema(ReaderInterface $reader): void
    {
        $r = new Registry($this->dbal);
        (new Entities($this->locator, $reader))->run($r);

        $this->assertTrue($r->hasEntity(Complete::class));
        $this->assertTrue($r->hasEntity('eComplete'));

        $this->assertSame(CompleteMapper::class, $r->getEntity('eComplete')->getMapper());
        $this->assertSame(CompleteRepository::class, $r->getEntity('eComplete')->getRepository());
        $this->assertSame(TestSource::class, $r->getEntity('eComplete')->getSource());
        $this->assertSame(SomeConstrain::class, $r->getEntity('eComplete')->getScope());

        $this->assertTrue($r->hasTable($r->getEntity('eComplete')));
        $this->assertSame('secondary', $r->getDatabase($r->getEntity('eComplete')));
        $this->assertSame('complete_data', $r->getTable($r->getEntity('eComplete')));

        $this->assertTrue($r->getEntity('eComplete')->getFields()->has('id'));
        $this->assertTrue($r->getEntity('eComplete')->getFields()->has('name'));

        $this->assertSame(
            'username',
            $r->getEntity('eComplete')->getFields()->get('name')->getColumn()
        );

        $this->assertFalse($r->getEntity('eComplete')->getFields()->has('ignored'));
    }
}
