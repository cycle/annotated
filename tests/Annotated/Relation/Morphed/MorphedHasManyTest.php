<?php declare(strict_types=1);
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Cycle\Annotated\Tests\Relation\Morphed;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Relation\Morphed\MorphedHasMany;
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
use Cycle\Schema\Relation\Morphed\MorphedHasMany as MorphedHasManyRelation;
use Spiral\Annotations\Parser;

abstract class MorphedHasManyTest extends BaseTest
{
    public function testRelation()
    {
        $p = new Parser();
        $p->register(new Entity());
        $p->register(new Column());
        $p->register(new Table());
        $p->register(new MorphedHasMany());

        $r = new Registry($this->dbal);

        $schema = (new Compiler())->compile($r, [
            new Entities($this->locator, $p),
            new CleanTables(),
            new Columns($p),
            new GenerateRelations(['morphedHasMany' => new MorphedHasManyRelation()]),
            new RenderTables(),
            new RenderRelations(),
            new Indexes($p),
            new SyncTables(),
            new GenerateTypecast(),
        ]);

        $this->assertArrayHasKey('labels', $schema['simple'][Schema::RELATIONS]);
        $this->assertSame(Relation::MORPHED_HAS_MANY, $schema['simple'][Schema::RELATIONS]['labels'][Relation::TYPE]);
        $this->assertSame("label", $schema['simple'][Schema::RELATIONS]['labels'][Relation::TARGET]);

        $this->assertArrayHasKey('labels', $schema['withTable'][Schema::RELATIONS]);
        $this->assertSame(
            Relation::MORPHED_HAS_MANY,
            $schema['withTable'][Schema::RELATIONS]['labels'][Relation::TYPE]
        );

        $this->assertSame("label", $schema['withTable'][Schema::RELATIONS]['labels'][Relation::TARGET]);

        $this->assertTrue(
            $this->dbal->database('default')
                       ->getDriver()
                       ->getSchema('labels')
                       ->hasColumn('owner_id')
        );

        $this->assertTrue(
            $this->dbal->database('default')
                       ->getDriver()
                       ->getSchema('labels')
                       ->hasColumn('owner_role')
        );

        $this->assertTrue(
            $this->dbal->database('default')
                       ->getDriver()
                       ->getSchema('labels')
                       ->hasIndex(['owner_id', 'owner_role'])
        );
    }
}