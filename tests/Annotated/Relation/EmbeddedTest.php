<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */
declare(strict_types=1);

namespace Cycle\Annotated\Tests\Relation;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Embeddable;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Relation\Embedded;
use Cycle\Annotated\Annotation\Table;
use Cycle\Annotated\Embeddings;
use Cycle\Annotated\Entities;
use Cycle\Annotated\MergeColumns;
use Cycle\Annotated\MergeIndexes;
use Cycle\Annotated\Tests\BaseTest;
use Cycle\ORM\Relation;
use Cycle\ORM\Schema;
use Cycle\Schema\Compiler;
use Cycle\Schema\Generator\GenerateRelations;
use Cycle\Schema\Generator\GenerateTypecast;
use Cycle\Schema\Generator\RenderRelations;
use Cycle\Schema\Generator\RenderTables;
use Cycle\Schema\Generator\ResetTables;
use Cycle\Schema\Generator\SyncTables;
use Cycle\Schema\Registry;
use Cycle\Schema\Relation\Embedded as EmbeddedRelation;
use Spiral\Annotations\Parser;
use Spiral\Tokenizer\Config\TokenizerConfig;
use Spiral\Tokenizer\Tokenizer;

abstract class EmbeddedTest extends BaseTest
{
    public function testRelation()
    {
        $this->enableProfiling();
        $tokenizer = new Tokenizer(new TokenizerConfig([
            'directories' => [__DIR__ . '/../Fixtures6'],
            'exclude'     => [],
        ]));

        $locator = $tokenizer->classLocator();

        $p = new Parser();
        $p->register(new Entity());
        $p->register(new Column());
        $p->register(new Table());
        $p->register(new Embeddable());
        $p->register(new Embedded());

        $r = new Registry($this->dbal);

        $schema = (new Compiler())->compile($r, [
            new Embeddings($locator, $p),
            new Entities($locator, $p),
            new ResetTables(),
            new MergeColumns($p),
            new GenerateRelations(['embedded' => new EmbeddedRelation()]),
            new RenderTables(),
            new RenderRelations(),
            new MergeIndexes($p),
            new SyncTables(),
            new GenerateTypecast(),
        ]);

        $this->assertArrayHasKey('address', $schema['user'][Schema::RELATIONS]);
        $this->assertSame(Relation::EMBEDDED, $schema['user'][Schema::RELATIONS]['address'][Relation::TYPE]);

        $this->assertSame("user.address", $schema['user'][Schema::RELATIONS]['address'][Relation::TARGET]);
        $this->assertSame(Relation::LOAD_EAGER, $schema['user'][Schema::RELATIONS]['address'][Relation::LOAD]);
    }

    public function testRelationLazyLoad()
    {
        $this->enableProfiling();
        $tokenizer = new Tokenizer(new TokenizerConfig([
            'directories' => [__DIR__ . '/../Fixtures7'],
            'exclude'     => [],
        ]));

        $locator = $tokenizer->classLocator();

        $p = new Parser();
        $p->register(new Entity());
        $p->register(new Column());
        $p->register(new Table());
        $p->register(new Embeddable());
        $p->register(new Embedded());

        $r = new Registry($this->dbal);

        $schema = (new Compiler())->compile($r, [
            new Embeddings($locator, $p),
            new Entities($locator, $p),
            new ResetTables(),
            new MergeColumns($p),
            new GenerateRelations(['embedded' => new EmbeddedRelation()]),
            new RenderTables(),
            new RenderRelations(),
            new MergeIndexes($p),
            new SyncTables(),
            new GenerateTypecast(),
        ]);

        $this->assertArrayHasKey('address', $schema['user'][Schema::RELATIONS]);
        $this->assertSame(Relation::EMBEDDED, $schema['user'][Schema::RELATIONS]['address'][Relation::TYPE]);

        $this->assertSame("user.address", $schema['user'][Schema::RELATIONS]['address'][Relation::TARGET]);
        $this->assertSame(Relation::LOAD_PROMISE, $schema['user'][Schema::RELATIONS]['address'][Relation::LOAD]);
    }
}