<?php declare(strict_types=1);
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Cycle\Annotated\Tests\Relation;

use Cycle\Annotated\Columns;
use Cycle\Annotated\Entities;
use Cycle\Annotated\Generator;
use Cycle\Annotated\Indexes;
use Cycle\Annotated\Tests\BaseTest;
use Cycle\Annotated\Tests\Fixtures2\MarkedInterface;
use Cycle\ORM\Relation;
use Cycle\ORM\Schema;
use Cycle\Schema\Compiler;
use Cycle\Schema\Generator\CleanTables;
use Cycle\Schema\Generator\GenerateRelations;
use Cycle\Schema\Generator\GenerateTypecast;
use Cycle\Schema\Generator\RenderRelations;
use Cycle\Schema\Generator\RenderTables;
use Cycle\Schema\Generator\SyncTables;
use Cycle\Schema\Generator\ValidateEntities;
use Cycle\Schema\Registry;
use Spiral\Tokenizer\Config\TokenizerConfig;
use Spiral\Tokenizer\Tokenizer;

abstract class InverseTest extends BaseTest
{
    public function testBelongsToOne()
    {
        $tokenizer = new Tokenizer(new TokenizerConfig([
            'directories' => [__DIR__ . '/../Fixtures2'],
            'exclude'     => [],
        ]));

        $locator = $tokenizer->classLocator();

        $p = Generator::defaultParser();
        $r = new Registry($this->dbal);

        $schema = (new Compiler())->compile($r, [
            new Entities($locator, $p),
            new CleanTables(),
            new Columns($p),
            GenerateRelations::defaultGenerator(),
            new ValidateEntities(),
            new RenderTables(),
            new RenderRelations(),
            new Indexes($p),
            new SyncTables(),
            new GenerateTypecast(),
        ]);

        $this->assertArrayHasKey('parent', $schema['eComplete'][Schema::RELATIONS]);
        $this->assertSame(Relation::BELONGS_TO, $schema['eComplete'][Schema::RELATIONS]['parent'][Relation::TYPE]);
        $this->assertSame("simple", $schema['eComplete'][Schema::RELATIONS]['parent'][Relation::TARGET]);

        $this->assertArrayHasKey('child', $schema['simple'][Schema::RELATIONS]);
        $this->assertSame(Relation::HAS_ONE, $schema['simple'][Schema::RELATIONS]['child'][Relation::TYPE]);
        $this->assertSame("eComplete", $schema['simple'][Schema::RELATIONS]['child'][Relation::TARGET]);
    }

    public function testBelongsToMany()
    {
        $tokenizer = new Tokenizer(new TokenizerConfig([
            'directories' => [__DIR__ . '/../Fixtures2'],
            'exclude'     => [],
        ]));

        $locator = $tokenizer->classLocator();

        $p = Generator::defaultParser();
        $r = new Registry($this->dbal);

        $schema = (new Compiler())->compile($r, [
            new Entities($locator, $p),
            new CleanTables(),
            new Columns($p),
            GenerateRelations::defaultGenerator(),
            new ValidateEntities(),
            new RenderTables(),
            new RenderRelations(),
            new Indexes($p),
            new SyncTables(),
            new GenerateTypecast(),
        ]);

        $this->assertArrayHasKey('uncles', $schema['eComplete'][Schema::RELATIONS]);
        $this->assertSame(Relation::BELONGS_TO, $schema['eComplete'][Schema::RELATIONS]['uncles'][Relation::TYPE]);
        $this->assertSame("simple", $schema['eComplete'][Schema::RELATIONS]['uncles'][Relation::TARGET]);

        $this->assertArrayHasKey('stepKids', $schema['simple'][Schema::RELATIONS]);
        $this->assertSame(Relation::HAS_MANY, $schema['simple'][Schema::RELATIONS]['stepKids'][Relation::TYPE]);
        $this->assertSame("eComplete", $schema['simple'][Schema::RELATIONS]['stepKids'][Relation::TARGET]);
    }

    public function testHasOne()
    {
        $tokenizer = new Tokenizer(new TokenizerConfig([
            'directories' => [__DIR__ . '/../Fixtures2'],
            'exclude'     => [],
        ]));

        $locator = $tokenizer->classLocator();

        $p = Generator::defaultParser();
        $r = new Registry($this->dbal);

        $schema = (new Compiler())->compile($r, [
            new Entities($locator, $p),
            new CleanTables(),
            new Columns($p),
            GenerateRelations::defaultGenerator(),
            new ValidateEntities(),
            new RenderTables(),
            new RenderRelations(),
            new Indexes($p),
            new SyncTables(),
            new GenerateTypecast(),
        ]);

        $this->assertArrayHasKey('simple', $schema['user'][Schema::RELATIONS]);
        $this->assertSame(Relation::HAS_ONE, $schema['user'][Schema::RELATIONS]['simple'][Relation::TYPE]);
        $this->assertSame("simple", $schema['user'][Schema::RELATIONS]['simple'][Relation::TARGET]);

        $this->assertArrayHasKey('user', $schema['simple'][Schema::RELATIONS]);
        $this->assertSame(Relation::BELONGS_TO, $schema['simple'][Schema::RELATIONS]['user'][Relation::TYPE]);
        $this->assertSame("user", $schema['simple'][Schema::RELATIONS]['user'][Relation::TARGET]);
    }

    public function testBelongsTo()
    {
        $tokenizer = new Tokenizer(new TokenizerConfig([
            'directories' => [__DIR__ . '/../Fixtures2'],
            'exclude'     => [],
        ]));

        $locator = $tokenizer->classLocator();

        $p = Generator::defaultParser();
        $r = new Registry($this->dbal);

        $schema = (new Compiler())->compile($r, [
            new Entities($locator, $p),
            new CleanTables(),
            new Columns($p),
            GenerateRelations::defaultGenerator(),
            new ValidateEntities(),
            new RenderTables(),
            new RenderRelations(),
            new Indexes($p),
            new SyncTables(),
            new GenerateTypecast(),
        ]);

        $this->assertArrayHasKey('owner', $schema['mark'][Schema::RELATIONS]);
        $this->assertSame(Relation::BELONGS_TO_MORPHED, $schema['mark'][Schema::RELATIONS]['owner'][Relation::TYPE]);
        $this->assertSame(MarkedInterface::class, $schema['mark'][Schema::RELATIONS]['owner'][Relation::TARGET]);

        $this->assertArrayHasKey('mark', $schema['user'][Schema::RELATIONS]);
        $this->assertSame(Relation::MORPHED_HAS_ONE, $schema['user'][Schema::RELATIONS]['mark'][Relation::TYPE]);
        $this->assertSame('mark', $schema['user'][Schema::RELATIONS]['mark'][Relation::TARGET]);

        $this->assertArrayHasKey('mark', $schema['simple'][Schema::RELATIONS]);
        $this->assertSame(Relation::MORPHED_HAS_ONE, $schema['simple'][Schema::RELATIONS]['mark'][Relation::TYPE]);
        $this->assertSame('mark', $schema['simple'][Schema::RELATIONS]['mark'][Relation::TARGET]);
    }
}