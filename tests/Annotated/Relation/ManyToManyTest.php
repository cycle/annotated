<?php declare(strict_types=1);
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Cycle\Annotated\Tests\Relation;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Relation\ManyToMany;
use Cycle\Annotated\Annotation\Table;
use Cycle\Annotated\Columns;
use Cycle\Annotated\Entities;
use Cycle\Annotated\Indexes;
use Cycle\Annotated\Tests\BaseTest;
use Cycle\ORM\Relation;
use Cycle\ORM\Schema;
use Cycle\Schema\Compiler;
use Cycle\Schema\Generator\CleanTables;
use Cycle\Schema\Generator\GenerateRelations;
use Cycle\Schema\Generator\GenerateTypecast;
use Cycle\Schema\Generator\RenderRelations;
use Cycle\Schema\Generator\RenderTables;
use Cycle\Schema\Generator\SyncTables;
use Cycle\Schema\Registry;
use Cycle\Schema\Relation\ManyToMany as ManyToManyRelation;
use Spiral\Annotations\Parser;

abstract class ManyToManyTest extends BaseTest
{
    public function testRelation()
    {
        $p = new Parser();
        $p->register(new Entity());
        $p->register(new Column());
        $p->register(new Table());
        $p->register(new ManyToMany());

        $r = new Registry($this->dbal);

        $schema = (new Compiler())->compile($r, [
            new Entities($this->locator, $p),
            new CleanTables(),
            new Columns($p),
            new GenerateRelations(['manyToMany' => new ManyToManyRelation()]),
            new RenderTables(),
            new RenderRelations(),
            new Indexes($p),
            new SyncTables(),
            new GenerateTypecast(),
        ]);

        $this->assertArrayHasKey('tags', $schema['withTable'][Schema::RELATIONS]);

        $this->assertSame(
            Relation::MANY_TO_MANY,
            $schema['withTable'][Schema::RELATIONS]['tags'][Relation::TYPE]
        );

        $this->assertSame(
            "tag",
            $schema['withTable'][Schema::RELATIONS]['tags'][Relation::TARGET]
        );


        $this->assertSame(
            "tagContext",
            $schema['withTable'][Schema::RELATIONS]['tags'][Relation::SCHEMA][Relation::THOUGH_ENTITY]
        );
    }
}